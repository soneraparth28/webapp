<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use App\Models\Categories;
use App\Models\User;
use App\Models\Media;
use App\Models\Plans;
use App\Models\Messages;
use App\Models\MediaMessages;
use App\Models\AdminSettings;
use App\Models\Subscriptions;
use App\Models\Notifications;
use App\Models\Updates;
use App\Models\PaymentGateways;
use App\Models\Languages;
use App\Models\Referrals;
use App\Models\ReferralTransactions;
use App\Helper;

class UpgradeController extends Controller
{
    public function __construct(AdminSettings $settings, Updates $updates, User $user) {
        $this->settings  = $settings::first();
        $this->user      = $user::first();
        $this->updates   = $updates::first();
    }
}
