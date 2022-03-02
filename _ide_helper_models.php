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
 * App\Models\Payment
 *
 * @property int $id
 * @property string $name
 * @property string $number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PaymentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Picture
 *
 * @property int $id
 * @property string $url
 * @property int $pictureable_id
 * @property string $pictureable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $pictureable
 * @method static \Illuminate\Database\Eloquent\Builder|Picture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Picture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Picture query()
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture wherePictureableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture wherePictureableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereUrl($value)
 */
	class Picture extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TopUp
 *
 * @property int $id
 * @property int $user_id
 * @property int $payment_id
 * @property string $payment_username
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Picture[] $pictures
 * @property-read int|null $pictures_count
 * @method static \Database\Factories\TopUpFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp query()
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp wherePaymentUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereUserId($value)
 */
	class TopUp extends \Eloquent {}
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

