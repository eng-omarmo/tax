<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Branch;
use App\Models\Tenant;
use App\Models\District;
use App\Models\Landlord;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GlobalCheckController extends Controller
{
    use ApiResponseTrait;

    /**
     * Check if an email exists globally in the system
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return $this->unprocessableResponse($validator->errors(), 'Validation failed');
            }

            $email = $request->email;
            $exists = false;
            $foundIn = [];

            // Check in Users table
            $userExists = User::where('email', $email)->exists();
            if ($userExists) {
                $exists = true;
                $foundIn[] = 'users';
            }

            // Check in Landlords table
            $landlordExists = Landlord::where('email', $email)->exists();
            if ($landlordExists) {
                $exists = true;
                $foundIn[] = 'landlords';
            }

            return $this->okResponse([
                'exists' => $exists,
                'found_in' => $foundIn
            ], $exists ? 'Email exists in the system' : 'Email does not exist in the system');

        } catch (\Exception $e) {
            return $this->errorResponse(null, 500, 'Failed to check email: ' . $e->getMessage());
        }
    }

    /**
     * Check if a phone number exists globally in the system
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPhone(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->unprocessableResponse($validator->errors(), 'Validation failed');
            }

            $phone = $request->phone;
            $exists = false;
            $foundIn = [];

            // Check in Users table
            $userExists = User::where('phone', $phone)->exists();
            if ($userExists) {
                $exists = true;
                $foundIn[] = 'users';
            }

            // Check in Landlords table
            $landlordExists = Landlord::where('phone_number', $phone)->exists();
            if ($landlordExists) {
                $exists = true;
                $foundIn[] = 'landlords';
            }

            return $this->okResponse([
                'exists' => $exists,
                'found_in' => $foundIn
            ], $exists ? 'Phone number exists in the system' : 'Phone number does not exist in the system');

        } catch (\Exception $e) {
            return $this->errorResponse(null, 500, 'Failed to check phone: ' . $e->getMessage());
        }
    }

  /**
 * Get branches for a specific district
 *
 * @param District $district District model instance (route model binding)
 * @return JsonResponse
 */
public function branch($district)
{
    $branches = Branch::where('district_id', $district)
        ->get();


    return $this->okResponse(
        $branches,
        'Branches fetched successfully'
    );
}
}
