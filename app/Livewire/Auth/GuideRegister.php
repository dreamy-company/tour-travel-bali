<?php

namespace App\Livewire\Auth;

use App\Enums\TariffMode;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\GuideProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.auth-large')]
#[Title('Register as Tour Guide')]
class GuideRegister extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;

    // Step 1: Account Creation
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $phone_number = '';

    // Step 2: Identity (Tier 1 KYC)
    public string $ktp_number = '';
    public string $bio = '';
    public array $languages = ['id', 'en'];
    /** @var mixed */
    public $ktp_photo;
    /** @var mixed */
    public $headshot;

    // Step 3: Legality (Tier 2 KYC)
    public string $ktpp_number = '';
    /** @var mixed */
    public $ktpp_file;
    public string $ktpp_expired_at = '';
    /** @var mixed */
    public $skck_file;
    public string $skck_expired_at = '';
    /** @var mixed */
    public $surat_sehat_file;

    // Step 4: Digital SOP Agreement
    public bool $signed_sop = false;

    /**
     * Get validation rules for the current step.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function getRulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
                'phone_number' => ['required', 'string', 'min:10', 'max:20'],
            ],
            2 => [
                'ktp_number' => ['required', 'string', 'regex:/^\d{16}$/'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'languages' => ['required', 'array', 'min:1'],
                'languages.*' => ['string', 'in:id,en,jp,fr,de'],
                'ktp_photo' => ['required', 'image', 'max:2048'], // 2MB
                'headshot' => ['required', 'image', 'max:2048'], // 2MB
            ],
            3 => [
                'ktpp_number' => ['required', 'string', 'max:100'],
                'ktpp_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // 5MB
                'ktpp_expired_at' => ['required', 'date', 'after:today'],
                'skck_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // 5MB
                'skck_expired_at' => ['required', 'date', 'after:today'],
                'surat_sehat_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // 5MB
            ],
            4 => [
                'signed_sop' => ['required', 'accepted'],
            ],
            default => [],
        };
    }

    /**
     * Validate a field in real-time.
     */
    public function updated(string $propertyName): void
    {
        $allRules = array_merge(
            $this->getRulesForStep(1),
            $this->getRulesForStep(2),
            $this->getRulesForStep(3),
            $this->getRulesForStep(4)
        );

        if (array_key_exists($propertyName, $allRules)) {
            $this->validateOnly($propertyName, [$propertyName => $allRules[$propertyName]]);
        }
    }

    /**
     * Move to the next step.
     */
    public function nextStep(): void
    {
        $rules = $this->getRulesForStep($this->currentStep);
        $this->validate($rules);

        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    /**
     * Move to the previous step.
     */
    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Complete registration.
     */
    public function register(): void
    {
        $rules = $this->getRulesForStep(4);
        $this->validate($rules);

        DB::transaction(function (): void {
            // 1. Create User as pending_verification
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'phone_number' => $this->phone_number,
                'role' => UserRole::GUIDE,
                'status' => UserStatus::PENDING_VERIFICATION,
            ]);

            // 2. Store Files to Private storage (local disk: maps to storage/app/private in Laravel 11+)
            /** @var \Illuminate\Http\UploadedFile $ktpPhotoFile */
            $ktpPhotoFile = $this->ktp_photo;
            $ktpPhotoPath = $ktpPhotoFile->store('guide_documents/ktp_photos', 'local');
            if ($ktpPhotoPath === false) {
                throw new \RuntimeException('Failed to store KTP photo.');
            }

            /** @var \Illuminate\Http\UploadedFile $headshotFile */
            $headshotFile = $this->headshot;
            $headshotPath = $headshotFile->store('guide_documents/headshots', 'local');
            if ($headshotPath === false) {
                throw new \RuntimeException('Failed to store headshot.');
            }

            /** @var \Illuminate\Http\UploadedFile $ktppFileObj */
            $ktppFileObj = $this->ktpp_file;
            $ktppFilePath = $ktppFileObj->store('guide_documents/ktpp_files', 'local');
            if ($ktppFilePath === false) {
                throw new \RuntimeException('Failed to store KTPP file.');
            }

            /** @var \Illuminate\Http\UploadedFile $skckFileObj */
            $skckFileObj = $this->skck_file;
            $skckFilePath = $skckFileObj->store('guide_documents/skck_files', 'local');
            if ($skckFilePath === false) {
                throw new \RuntimeException('Failed to store SKCK file.');
            }

            /** @var \Illuminate\Http\UploadedFile $suratSehatFileObj */
            $suratSehatFileObj = $this->surat_sehat_file;
            $suratSehatFilePath = $suratSehatFileObj->store('guide_documents/surat_sehat_files', 'local');
            if ($suratSehatFilePath === false) {
                throw new \RuntimeException('Failed to store Surat Sehat file.');
            }

            // 3. Create Guide Profile
            GuideProfile::create([
                'user_id' => $user->id,
                'ktp_number' => $this->ktp_number,
                'ktp_photo' => $ktpPhotoPath,
                'headshot' => $headshotPath,
                'ktpp_number' => $this->ktpp_number,
                'ktpp_file' => $ktppFilePath,
                'ktpp_expired_at' => $this->ktpp_expired_at,
                'skck_file' => $skckFilePath,
                'skck_expired_at' => $this->skck_expired_at,
                'surat_sehat_file' => $suratSehatFilePath,
                'bio' => $this->bio ?: null,
                'languages' => $this->languages,
                'tariff_mode' => TariffMode::DAILY, // default
                'base_rate' => 0.00,
                'is_verified' => false,
                'signed_sop_at' => now(),
            ]);
        });

        // Redirect to 'Under Review' landing page
        $this->redirect(route('register.under-review'), navigate: true);
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.auth.guide-register');
    }
}
