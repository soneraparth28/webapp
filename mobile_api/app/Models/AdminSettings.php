<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminSettings
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property int $update_length The max length of updates
 * @property string $status_page 0 Offline, 1 Online
 * @property string $email_verification 0 Off, 1 On
 * @property string $email_no_reply
 * @property string $email_admin
 * @property string $captcha
 * @property int $file_size_allowed Size in Bytes
 * @property string $google_analytics
 * @property string $paypal_account
 * @property string $twitter
 * @property string $facebook
 * @property string $pinterest
 * @property string $instagram
 * @property string $google_adsense
 * @property string $currency_symbol
 * @property string $currency_code
 * @property int $min_subscription_amount
 * @property string $payment_gateway
 * @property string $min_width_height_image
 * @property int $fee_commission
 * @property int $max_subscription_amount
 * @property string $date_format
 * @property string $link_privacy
 * @property string $link_terms
 * @property string $currency_position
 * @property string $facebook_login
 * @property int $amount_min_withdrawal
 * @property string $youtube
 * @property string $github
 * @property int $comment_length
 * @property int $days_process_withdrawals
 * @property string $google_login
 * @property int $number_posts_show
 * @property int $number_comments_show
 * @property string $registration_active 0 No, 1 Yes
 * @property string $account_verification 0 No, 1 Yes
 * @property string $logo
 * @property string $logo_2
 * @property string $favicon
 * @property string $home_index
 * @property string $bg_gradient
 * @property string $img_1
 * @property string $img_2
 * @property string $img_3
 * @property string $img_4
 * @property string $avatar
 * @property string $show_counter
 * @property string $color_default
 * @property string $decimal_format
 * @property string $version
 * @property string $link_cookies
 * @property int $story_length
 * @property string $maintenance_mode
 * @property string $company
 * @property string $country
 * @property string $address
 * @property string $city
 * @property string $zip
 * @property string $vat
 * @property string $widget_creators_featured
 * @property int $home_style
 * @property int $file_size_allowed_verify_account
 * @property string $payout_method_paypal
 * @property string $payout_method_bank
 * @property int $min_tip_amount
 * @property int $max_tip_amount
 * @property int $min_ppv_amount
 * @property int $max_ppv_amount
 * @property int $min_deposits_amount
 * @property int $max_deposits_amount
 * @property string $button_style
 * @property string $twitter_login
 * @property string $hide_admin_profile
 * @property string $requests_verify_account
 * @property string $navbar_background_color
 * @property string $navbar_text_color
 * @property string $footer_background_color
 * @property string $footer_text_color
 * @property string $preloading
 * @property string $preloading_image
 * @property string $watermark
 * @property string $earnings_simulator
 * @property string $custom_css
 * @property string $custom_js
 * @property string $alert_adult
 * @property string $genders
 * @property string $cover_default
 * @property string $who_can_see_content
 * @property string $users_can_edit_post
 * @property string $disable_wallet
 * @property string $disable_banner_cookies
 * @property string $wallet_format
 * @property int $maximum_files_post
 * @property int $maximum_files_msg
 * @property string $announcement
 * @property string $announcement_show
 * @property string $announcement_cookie
 * @property int $limit_categories
 * @property string $captcha_contact
 * @property string $disable_tips
 * @property string $payout_method_payoneer
 * @property string $payout_method_zelle
 * @property string $type_announcement
 * @property string $referral_system
 * @property string $auto_approve_post
 * @property string $watermark_on_videos
 * @property int $percentage_referred
 * @property string $referral_transaction_limit
 * @property string $video_encoding
 * @property string $live_streaming_status
 * @property int $live_streaming_minimum_price
 * @property int $live_streaming_max_price
 * @property string $agora_app_id
 * @property string $tiktok
 * @property string $snapchat
 * @property int $limit_live_streaming_paid
 * @property int $limit_live_streaming_free
 * @property string $live_streaming_free
 * @property string $type_withdrawals
 * @property int $shop
 * @property int $min_price_product
 * @property int $max_price_product
 * @property int $digital_product_sale
 * @property int $custom_content
 * @property int $tax_on_wallet
 * @property int $stripe_connect
 * @property string $stripe_connect_countries
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereAccountVerification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereAgoraAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereAlertAdult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereAmountMinWithdrawal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereAnnouncement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereAnnouncementCookie($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereAnnouncementShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereAutoApprovePost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereBgGradient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereButtonStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCaptcha($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCaptchaContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereColorDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCommentLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCoverDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCurrencyPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCurrencySymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCustomContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCustomCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereCustomJs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereDateFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereDaysProcessWithdrawals($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereDecimalFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereDigitalProductSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereDisableBannerCookies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereDisableTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereDisableWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereEarningsSimulator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereEmailAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereEmailNoReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereEmailVerification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereFacebookLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereFavicon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereFeeCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereFileSizeAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereFileSizeAllowedVerifyAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereFooterBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereFooterTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereGenders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereGithub($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereGoogleAdsense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereGoogleAnalytics($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereGoogleLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereHideAdminProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereHomeIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereHomeStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereImg1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereImg2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereImg3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereImg4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLimitCategories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLimitLiveStreamingFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLimitLiveStreamingPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLinkCookies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLinkPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLinkTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLiveStreamingFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLiveStreamingMaxPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLiveStreamingMinimumPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLiveStreamingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereLogo2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMaintenanceMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMaxDepositsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMaxPpvAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMaxPriceProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMaxSubscriptionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMaxTipAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMaximumFilesMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMaximumFilesPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMinDepositsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMinPpvAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMinPriceProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMinSubscriptionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMinTipAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereMinWidthHeightImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereNavbarBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereNavbarTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereNumberCommentsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereNumberPostsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings wherePaymentGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings wherePayoutMethodBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings wherePayoutMethodPayoneer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings wherePayoutMethodPaypal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings wherePayoutMethodZelle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings wherePaypalAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings wherePercentageReferred($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings wherePinterest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings wherePreloading($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings wherePreloadingImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereReferralSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereReferralTransactionLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereRegistrationActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereRequestsVerifyAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereShop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereShowCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereSnapchat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereStatusPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereStoryLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereStripeConnect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereStripeConnectCountries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereTaxOnWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereTiktok($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereTwitterLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereTypeAnnouncement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereTypeWithdrawals($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereUpdateLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereUsersCanEditPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereVideoEncoding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereWalletFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereWatermark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereWatermarkOnVideos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereWhoCanSeeContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereWidgetCreatorsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereYoutube($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminSettings whereZip($value)
 * @mixin \Eloquent
 */
class AdminSettings extends Model {

	protected $guarded = array();
	public $timestamps = false;
}
