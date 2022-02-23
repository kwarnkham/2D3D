<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\AccountProvider
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\AccountProviderFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountProvider whereUpdatedAt($value)
 */
	class AccountProvider extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AccountProvider[] $accountProviders
 * @property-read int|null $account_providers_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserProvider
 *
 * @property int $id
 * @property int $user_id
 * @property int $account_provider_id
 * @property int $provider_id
 * @property string $username
 * @property \Illuminate\Support\Carbon $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserProvider whereAccountProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProvider whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProvider whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProvider whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProvider whereUsername($value)
 */
	class UserProvider extends \Eloquent {}
}

