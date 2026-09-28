<?php

namespace App\Actions\Fortify;

use App\Actions\AffiliateUserAction;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\DTOs\AffiliationData;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(
        protected AffiliateUserAction $affiliateUserAction,
    ) {}

    /**
     * Validate and create a newly registered user with binary and unilevel placement.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        // Normalizar pierna binaria si viene como 'left' o 'right'
        if (isset($input['binary_leg'])) {
            $normalizedLeg = strtolower(trim((string) $input['binary_leg']));
            if ($normalizedLeg === 'left') {
                $input['binary_leg'] = 'L';
            } elseif ($normalizedLeg === 'right') {
                $input['binary_leg'] = 'R';
            } else {
                $input['binary_leg'] = strtoupper($normalizedLeg);
            }
        }

        $isMaster = ! $this->affiliateUserAction->isMasterRegistered();

        $rules = [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'last_name' => ['nullable', 'string', 'max:255'],
            'document_type_id' => ['nullable', 'integer', 'exists:document_types,id'],
            'dni' => ['nullable', 'string', 'max:50'],
            'sex' => ['nullable', 'string', 'in:male,female,other'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_type' => ['nullable', 'string', 'max:50'],
            'account_number' => ['nullable', 'string', 'max:50'],
        ];

        // Validar unicidad compuesta para tipo de documento y DNI
        if (! empty($input['dni']) && ! empty($input['document_type_id'])) {
            $rules['dni'][] = Rule::unique('users', 'dni')
                ->where(fn ($query) => $query->where('document_type_id', $input['document_type_id']));
        }

        if (! $isMaster) {
            $rules['sponsor_username'] = [
                'required',
                'string',
                'exists:users,username',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $sponsor = User::where('username', $value)->first();
                    if ($sponsor && ! $sponsor->canSponsorOthers()) {
                        $fail('El patrocinador indicado aún no se encuentra activo en el árbol binario para patrocinar.');
                    }
                },
            ];
            $rules['binary_leg'] = ['required', 'string', 'in:L,R,l,r'];
        }

        Validator::make($input, $rules, [
            'sponsor_username.required' => 'Debes indicar el nombre de usuario de tu patrocinador.',
            'sponsor_username.exists' => 'El patrocinador indicado no existe en el sistema.',
            'binary_leg.required' => 'Debes seleccionar la pierna binaria (Izquierda o Derecha).',
            'binary_leg.in' => 'La pierna binaria seleccionada no es válida.',
            'dni.unique' => 'Ya existe un usuario registrado con este tipo y número de documento.',
            'birthdate.before' => 'La fecha de nacimiento debe ser anterior al día de hoy.',
        ])->validate();

        $affiliationData = AffiliationData::fromArray($input);

        // 1. Ejecutar acción de afiliación (colocación binaria y unilevel garantizada)
        $user = $this->affiliateUserAction->execute($affiliationData);

        // 2. Persistir datos adicionales de users
        $userUpdate = [];
        if (! empty($input['last_name'])) {
            $userUpdate['last_name'] = trim((string) $input['last_name']);
        }
        if (! empty($input['document_type_id'])) {
            $userUpdate['document_type_id'] = (int) $input['document_type_id'];
        }
        if (! empty($input['dni'])) {
            $userUpdate['dni'] = trim((string) $input['dni']);
        }
        if (! empty($userUpdate)) {
            $user->update($userUpdate);
        }

        // 3. Persistir registro en user_data
        $user->userData()->create([
            'sex' => ! empty($input['sex']) && in_array($input['sex'], ['male', 'female', 'other'], true) ? $input['sex'] : null,
            'birthdate' => ! empty($input['birthdate']) ? $input['birthdate'] : null,
            'phone' => ! empty($input['phone']) ? trim((string) $input['phone']) : null,
            'country_id' => ! empty($input['country_id']) ? (int) $input['country_id'] : null,
            'department_id' => ! empty($input['department_id']) ? (int) $input['department_id'] : null,
            'city_id' => ! empty($input['city_id']) ? (int) $input['city_id'] : null,
            'city' => ! empty($input['city']) ? trim((string) $input['city']) : null,
            'address' => ! empty($input['address']) ? trim((string) $input['address']) : null,
            'bank_name' => ! empty($input['bank_name']) ? trim((string) $input['bank_name']) : null,
            'account_type' => ! empty($input['account_type']) ? trim((string) $input['account_type']) : null,
            'account_number' => ! empty($input['account_number']) ? trim((string) $input['account_number']) : null,
        ]);

        return $user;
    }
}
