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
 * App\Models\AppSetting
 *
 * @property int $id
 * @property float $pool_amount
 * @property string|null $config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting wherePoolAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereUpdatedAt($value)
 */
	class AppSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AppVersion
 *
 * @property int $id
 * @property string $version
 * @property string|null $note
 * @property int $optional
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereOptional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereVersion($value)
 */
	class AppVersion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ApprovedTopUp
 *
 * @property int $id
 * @property int $top_up_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Picture|null $picture
 * @property-read \App\Models\TopUp $top_up
 * @method static \Illuminate\Database\Eloquent\Builder|ApprovedTopUp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApprovedTopUp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApprovedTopUp query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApprovedTopUp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApprovedTopUp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApprovedTopUp whereTopUpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApprovedTopUp whereUpdatedAt($value)
 */
	class ApprovedTopUp extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Jackpot
 *
 * @property-read \App\Models\TwoDigit|null $twoDigit
 * @method static \Illuminate\Database\Eloquent\Builder|Jackpot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Jackpot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Jackpot query()
 */
	class Jackpot extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\JackpotNumber
 *
 * @method static \Illuminate\Database\Eloquent\Builder|JackpotNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JackpotNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JackpotNumber query()
 */
	class JackpotNumber extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\JackpotReward
 *
 * @property-read \App\Models\PointLog|null $point_log
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TwoDigit[] $twoDigits
 * @property-read int|null $two_digits_count
 * @method static \Illuminate\Database\Eloquent\Builder|JackpotReward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JackpotReward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JackpotReward query()
 */
	class JackpotReward extends \Eloquent implements \App\Contracts\PointLogable {}
}

namespace App\Models{
/**
 * App\Models\PasswordChange
 *
 * @property int $id
 * @property int $user_id
 * @property int $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PasswordChangeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange whereUserId($value)
 */
	class PasswordChange extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Payment
 *
 * @property int $id
 * @property string $name
 * @property string|null $mm_name
 * @property int $type
 * @property string|null $number
 * @property string|null $account_name
 * @property string|null $qr
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PaymentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereMmName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereQr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Picture
 *
 * @property int $id
 * @property string $name
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
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture wherePictureableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture wherePictureableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereUpdatedAt($value)
 */
	class Picture extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Point
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\PointFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Point newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Point newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Point query()
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereUpdatedAt($value)
 */
	class Point extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PointLog
 *
 * @property int $id
 * @property int $user_id
 * @property int $point_id
 * @property float $amount
 * @property int $type
 * @property string|null $note
 * @property int|null $point_loggable_id
 * @property string|null $point_loggable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Point $point
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $point_loggable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog of(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog wherePointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog wherePointLoggableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog wherePointLoggableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PointLog whereUserId($value)
 */
	class PointLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReferralReward
 *
 * @property int $id
 * @property float $amount
 * @property float $rate
 * @property int $referrer_id
 * @property int $referee_id
 * @property int $point_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Point $point
 * @property-read \App\Models\PointLog|null $point_log
 * @property-read \App\Models\User $referee
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralReward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralReward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralReward query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralReward whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralReward whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralReward whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralReward wherePointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralReward whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralReward whereRefereeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralReward whereReferrerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralReward whereUpdatedAt($value)
 */
	class ReferralReward extends \Eloquent implements \App\Contracts\PointLogable {}
}

namespace App\Models{
/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\RoleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TopUp
 *
 * @property int $id
 * @property int $user_id
 * @property int $payment_id
 * @property int $status
 * @property float $amount
 * @property string|null $denied_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ApprovedTopUp|null $approved_top_up
 * @property-read \App\Models\Payment $payment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Picture[] $pictures
 * @property-read int|null $pictures_count
 * @property-read \App\Models\PointLog|null $point_log
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\TopUpFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp of(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp query()
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereDeniedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopUp whereUserId($value)
 */
	class TopUp extends \Eloquent implements \App\Contracts\PointLogable {}
}

namespace App\Models{
/**
 * App\Models\TwoDigit
 *
 * @property int $id
 * @property int $user_id
 * @property int $number
 * @property float $amount
 * @property int $point_id
 * @property int|null $two_digit_hit_id
 * @property int|null $jack_pot_reward_id
 * @property string|null $settled_at
 * @property string|null $jack_potted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Jackpot|null $jackPot
 * @property-read \App\Models\JackpotReward|null $jackPotReward
 * @property-read \App\Models\Point $point
 * @property-read \App\Models\PointLog|null $point_log
 * @property-read \App\Models\TwoDigitHit|null $twoDigitHit
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\TwoDigitFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit of(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit query()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit whereJackPotRewardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit whereJackPottedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit wherePointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit whereSettledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit whereTwoDigitHitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigit whereUserId($value)
 */
	class TwoDigit extends \Eloquent implements \App\Contracts\PointLogable {}
}

namespace App\Models{
/**
 * App\Models\TwoDigitHit
 *
 * @property int $id
 * @property int $number
 * @property int $rate
 * @property string $day
 * @property int $morning
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TwoDigit[] $twoDigits
 * @property-read int|null $two_digits_count
 * @method static \Database\Factories\TwoDigitHitFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigitHit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigitHit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigitHit query()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigitHit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigitHit whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigitHit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigitHit whereMorning($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigitHit whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigitHit whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoDigitHit whereUpdatedAt($value)
 */
	class TwoDigitHit extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property int|null $referrer_id
 * @property string $name
 * @property string $locale
 * @property string|null $banned_at
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AccountProvider[] $accountProviders
 * @property-read int|null $account_providers_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PasswordChange[] $passwordChanges
 * @property-read int|null $password_changes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PointLog[] $pointLogs
 * @property-read int|null $point_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Point[] $points
 * @property-read int|null $points_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $referees
 * @property-read int|null $referees_count
 * @property-read User|null $referrer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TwoDigit[] $twoDigits
 * @property-read int|null $two_digits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Withdraw[] $withdraws
 * @property-read int|null $withdraws_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBannedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereReferrerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Translation\HasLocalePreference {}
}

namespace App\Models{
/**
 * App\Models\UserProvider
 *
 * @property int $id
 * @property int $user_id
 * @property int $account_provider_id
 * @property int $provider_id
 * @property string|null $username
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

namespace App\Models{
/**
 * App\Models\Withdraw
 *
 * @property int $id
 * @property int $user_id
 * @property int $point_id
 * @property int $payment_id
 * @property float $amount
 * @property string|null $username
 * @property string $account
 * @property int $status
 * @property string|null $denied_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Payment $payment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Picture[] $pictures
 * @property-read int|null $pictures_count
 * @property-read \App\Models\Point $point
 * @property-read \App\Models\PointLog|null $point_log
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw of(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw query()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereDeniedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw wherePointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereUsername($value)
 */
	class Withdraw extends \Eloquent implements \App\Contracts\PointLogable {}
}

