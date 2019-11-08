<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Kno;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class KnoUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials)
    {
        if (!isset($credentials['kno_token'])) {
            return null;
        }

        $knoToken = $credentials['kno_token'];
        $apiToken = env('KNO_API_TOKEN');
        $authHeader = base64_encode($apiToken . ":");

        try {
            $response = file_get_contents('https://api.trykno.app/v0/pass', false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => [
                        "Content-type: application/json",
                        "Authorization: Basic $authHeader",
                    ],
                    'content' => json_encode([
                        'token' => $knoToken
                    ])
                ]
            ]));
            $json = json_decode($response);

            $personaId = $json->persona->id;

            $model = $this->createModel();
            $retrievedModel =
                $this->newModelQuery($model)
                    ->where('kno_user_id', $personaId)
                    ->first();


            if (!$retrievedModel) {
                $model->kno_user_id = $personaId;
                $model->save();
                return $model;
            }

            return $retrievedModel;
        } catch (\Exception $e) {
            // TODO: Super bad. Just swallowing the exception
            return null;
        }
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // Not applicable
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        // Not applicable
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        // Always true if we've retrieved an instance of the user successfully
        return true;
    }
}