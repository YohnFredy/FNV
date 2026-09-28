<?php

namespace App\Livewire\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\Parish;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RegisterForm extends Component
{
    public ?string $sponsor_username = '';

    public ?string $binary_leg = 'L';

    public bool $isMaster = false;

    public string $name = '';

    public string $last_name = '';

    public ?int $document_type_id = null;

    public string $dni = '';

    public string $sex = '';

    public ?string $birthdate = '';

    public string $phone = '';

    public ?int $country_id = null;

    public ?int $department_id = null;

    public ?int $city_id = null;

    public ?int $parish_id = null;

    public string $address = '';

    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $terms_accepted = false;

    /**
     * Estado y datos para el Modal de Bienvenida tras registro exitoso.
     */
    public bool $showSuccessModal = false;

    public bool $wasAuthenticatedBefore = false;

    public ?string $registeredName = null;

    public ?string $registeredUsername = null;

    public ?string $registeredEmail = null;

    public ?string $registeredSponsor = null;

    public ?string $registeredLeg = null;

    /**
     * Estados de validación en tiempo real.
     *
     * @var array{valid: bool, message: string}|null
     */
    public ?array $usernameStatus = null;

    /**
     * @var array{valid: bool, message: string}|null
     */
    public ?array $emailStatus = null;

    /**
     * @var array{valid: bool, message: string}|null
     */
    public ?array $passwordStatus = null;

    /**
     * @var array{valid: bool, message: string}|null
     */
    public ?array $passwordMatchStatus = null;

    public ?string $sponsorError = null;

    public function mount(?string $sponsor = null, ?string $leg = null, bool $isMaster = false): void
    {
        $this->isMaster = $isMaster;
        $this->sponsor_username = $sponsor ?: '';

        if (! $this->isMaster && ! empty($this->sponsor_username)) {
            $sp = User::where('username', $this->sponsor_username)->first();
            if ($sp && ! $sp->canSponsorOthers()) {
                $this->sponsorError = "El usuario '{$this->sponsor_username}' se encuentra en la Sala de Espera y aún no puede patrocinar a nuevos miembros.";
            } elseif (! $sp) {
                $this->sponsorError = "El patrocinador '{$this->sponsor_username}' no existe en el sistema.";
            }
        }

        $normalizedLeg = strtoupper((string) $leg);
        $this->binary_leg = in_array($normalizedLeg, ['R', 'RIGHT'], true) ? 'R' : 'L';

        // País por defecto (Colombia o primer país disponible)
        $defaultCountry = Country::where('name', 'like', '%Colombia%')
            ->orWhere('country_code', 'COL')
            ->first() ?? Country::first();
        $this->country_id = $defaultCountry?->id;

        // Tipo de documento por defecto
        $defaultDoc = DocumentType::where('is_default', true)->first() ?? DocumentType::first();
        $this->document_type_id = $defaultDoc?->id;
    }

    public function updatedCountryId(): void
    {
        $this->department_id = null;
        $this->city_id = null;
        $this->parish_id = null;
    }

    public function updatedDepartmentId(): void
    {
        $this->city_id = null;
        $this->parish_id = null;
    }

    public function updatedCityId(): void
    {
        $this->parish_id = null;
    }

    public function updatedUsername(string $value): void
    {
        $val = strtolower(trim($value));
        if ($val === '') {
            $this->usernameStatus = null;

            return;
        }

        if (strlen($val) < 3) {
            $this->usernameStatus = ['valid' => false, 'message' => 'El usuario debe tener al menos 3 caracteres.'];

            return;
        }

        if (! preg_match('/^[a-zA-Z0-9_\-]+$/', $val)) {
            $this->usernameStatus = ['valid' => false, 'message' => 'Solo se permiten letras, números, guiones y guiones bajos.'];

            return;
        }

        if (User::where('username', $val)->exists()) {
            $this->usernameStatus = ['valid' => false, 'message' => "❌ El usuario '{$val}' ya se encuentra registrado. Elige otro."];
        } else {
            $this->usernameStatus = ['valid' => true, 'message' => '✓ ¡Nombre de usuario disponible!'];
        }
    }

    public function updatedEmail(string $value): void
    {
        $val = strtolower(trim($value));
        if ($val === '') {
            $this->emailStatus = null;

            return;
        }

        if (! filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->emailStatus = ['valid' => false, 'message' => 'Ingresa un formato de correo válido (ej: nombre@dominio.com).'];

            return;
        }

        if (User::where('email', $val)->exists()) {
            $this->emailStatus = ['valid' => false, 'message' => '❌ Este correo ya está registrado en el sistema.'];
        } else {
            $this->emailStatus = ['valid' => true, 'message' => '✓ Correo electrónico disponible.'];
        }
    }

    public function updatedPassword(string $value): void
    {
        if ($value === '') {
            $this->passwordStatus = null;
        } elseif (strlen($value) < 8) {
            $this->passwordStatus = ['valid' => false, 'message' => 'La contraseña debe tener mínimo 8 caracteres.'];
        } else {
            $this->passwordStatus = ['valid' => true, 'message' => '✓ Longitud de contraseña adecuada.'];
        }

        if ($this->password_confirmation !== '') {
            $this->checkPasswordMatch();
        }
    }

    public function updatedPasswordConfirmation(string $value): void
    {
        $this->checkPasswordMatch();
    }

    protected function checkPasswordMatch(): void
    {
        if ($this->password_confirmation === '') {
            $this->passwordMatchStatus = null;
        } elseif ($this->password !== $this->password_confirmation) {
            $this->passwordMatchStatus = ['valid' => false, 'message' => '❌ Las contraseñas no coinciden.'];
        } else {
            $this->passwordMatchStatus = ['valid' => true, 'message' => '✓ ¡Las contraseñas coinciden perfectamente!'];
        }
    }

    /**
     * @return Collection<int, DocumentType>
     */
    #[Computed]
    public function documentTypes(): Collection
    {
        return DocumentType::where('is_active', true)->orderBy('id')->get();
    }

    /**
     * @return Collection<int, Country>
     */
    #[Computed]
    public function countries(): Collection
    {
        return Country::orderBy('name')->get();
    }

    /**
     * @return Collection<int, Department>
     */
    #[Computed]
    public function departments(): Collection
    {
        if (! $this->country_id) {
            return Department::orderBy('name')->get();
        }

        return Department::where('country_id', $this->country_id)->orderBy('name')->get();
    }

    /**
     * @return Collection<int, City>
     */
    #[Computed]
    public function cities(): Collection
    {
        if (! $this->department_id) {
            return new Collection;
        }

        return City::where('department_id', $this->department_id)->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Parish>
     */
    #[Computed]
    public function parishes(): Collection
    {
        if (! $this->city_id) {
            return new Collection;
        }

        return Parish::where('city_id', $this->city_id)->orderBy('name')->get();
    }

    #[Computed]
    public function selectedCountry(): ?Country
    {
        if (! $this->country_id) {
            return null;
        }

        return Country::find($this->country_id);
    }

    #[Computed]
    public function divisionTerm1(): string
    {
        return $this->selectedCountry()?->division_term_1 ?: 'Departamento';
    }

    #[Computed]
    public function divisionTerm2(): string
    {
        return $this->selectedCountry()?->division_term_2 ?: 'Ciudad';
    }

    #[Computed]
    public function divisionTerm3(): string
    {
        return $this->selectedCountry()?->division_term_3 ?: 'Parroquia / Localidad';
    }

    #[Computed]
    public function hasParishes(): bool
    {
        if (! $this->country_id || ! $this->department_id || ! $this->city_id) {
            return false;
        }

        return $this->parishes()->isNotEmpty();
    }

    public function updatedTermsAccepted(): void
    {
        $this->resetErrorBag('terms_accepted');
    }

    public function register(CreateNewUser $creator): mixed
    {
        $this->resetErrorBag('terms_accepted');

        if (! $this->terms_accepted) {
            $this->addError('terms_accepted', 'Debes aceptar los Términos y Condiciones y el Contrato de Afiliación para continuar.');

            return null;
        }

        if (! $this->isMaster && $this->sponsorError) {
            $this->addError('sponsor_username', $this->sponsorError);

            return null;
        }

        $cityName = '';
        if ($this->parish_id) {
            $parish = Parish::find($this->parish_id);
            $cityName = $parish instanceof Parish ? $parish->name : '';
        } elseif ($this->city_id) {
            $city = City::find($this->city_id);
            $cityName = $city instanceof City ? $city->name : '';
        }

        $input = [
            'sponsor_username' => $this->sponsor_username,
            'binary_leg' => $this->binary_leg,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'document_type_id' => $this->document_type_id,
            'dni' => $this->dni,
            'sex' => $this->sex,
            'birthdate' => $this->birthdate,
            'phone' => $this->phone,
            'country_id' => $this->country_id,
            'department_id' => $this->department_id,
            'city_id' => $this->city_id,
            'city' => $cityName,
            'address' => $this->address,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'terms_accepted' => $this->terms_accepted,
        ];

        $wasAuth = Auth::check();
        $this->wasAuthenticatedBefore = $wasAuth;

        $user = $creator->create($input);

        $this->registeredName = $user->name.' '.($user->last_name ?? '');
        $this->registeredUsername = $user->username;
        $this->registeredEmail = $user->email;
        $this->registeredSponsor = $this->sponsor_username;
        $this->registeredLeg = $this->binary_leg === 'R' ? 'Derecha (Right)' : 'Izquierda (Left)';

        // Si era un visitante sin sesión previa, lo autenticamos inmediatamente
        if (! $wasAuth) {
            Auth::login($user);
        }

        // Abrimos el modal de bienvenida
        $this->showSuccessModal = true;

        return null;
    }

    public function continueAfterRegister(): mixed
    {
        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.register-form');
    }
}
