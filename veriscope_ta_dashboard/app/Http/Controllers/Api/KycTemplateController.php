<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\{User, KycTemplate};

use Illuminate\Support\Facades\Log;


class KycTemplateController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        Log::debug('KycTemplateController index');

        // get all params
        $input = $request->all();

        // set defaults for pagination
        $page = !empty($input['page']) ? (int)$input['page'] : 1;
        $perPage = !empty($input['perPage']) ? (int)$input['perPage'] : 50;

        // build a users, and a paginated users collection

        $kycTemplates = new KycTemplate;
        $paginatedKycTemplates = new KycTemplate;

        // logic for searching
        // TODO: use a 3rd party search tool?
        if(!empty($input['searchTerm'])) {
            $kycTemplates = $kycTemplates->search($input['searchTerm']);
            $paginatedKycTemplates = $paginatedKycTemplates->search($input['searchTerm']);
        }

        // sort logic
        if(!empty($input['sort'])) {
          $sort = json_decode($input['sort']);
          if($sort->field != '' && $sort->type != '') {
            $paginatedKycTemplates = $paginatedKycTemplates->orderBy($sort->field, $sort->type);
          }
        }

        // apply pagination
        if($perPage !== -1) {
          $paginatedKycTemplates = $paginatedKycTemplates->offset(($page-1) * $perPage)->limit($perPage)->get();
        } else {
          $paginatedKycTemplates = $paginatedKycTemplates->get();
        }

        // add custom column for editing and verifying
        foreach($paginatedKycTemplates as $kyc_template) {

          $kyc_template['action'] = '<a href="/backoffice/kyctemplates/'.$kyc_template->id.'/details" class="btn btn--alt btn--sm">view</a> ';
          $kyc_template['crypto_wallet_address'] = $kyc_template->crypto_address;
        }

        // return the current params and rows back
        return [
          'serverParams' => [
            'sort' => !empty($input['sort']) ? $input['sort'] : '',
            'page' => $page,
            'perPage' => $perPage,
          ],
          'totalRecords' => $kycTemplates->count(),
          'rows' => $paginatedKycTemplates,
        ];
    }

    public function kyc_template_details(Request $request)
    {

        Log::debug('KycTemplateController kyc_template_details');

        // get all params
        $input = $request->all();

        Log::debug($input);
        $filter = explode('|', $input['filter']);
        $kyc_template_id = $filter[1];
        Log::debug($kyc_template_id);
        // set defaults for pagination
        $page = !empty($input['page']) ? (int)$input['page'] : 1;
        $perPage = !empty($input['perPage']) ? (int)$input['perPage'] : 50;

        // build a users, and a paginated users collection

        $result = KycTemplate::where('id', $kyc_template_id)->first();

        $list = [['field' => 'ID', 'data'  => $result['id']],
                    ['field' => 'Created On', 'data'  => $result['created_at']],
                    ['field' => 'Updated At', 'data'  => $result['updated_at']],
                    ['field' => 'Attestation Hash', 'data'  => $result['attestation_hash']],
                    ['field' => 'Beneficiary TA Address', 'data'  => $result['beneficiary_ta_address']],
                    ['field' => 'Beneficiary TA Public Key', 'data' => $result['beneficiary_ta_public_key']],
                    ['field' => 'Beneficiary User Address', 'data'  => $result['beneficiary_user_address']],
                    ['field' => 'Beneficiary User Public Key', 'data'  => $result['beneficiary_user_public_key']],
                    ['field' => 'Beneficiary TA Signature Hash', 'data'  => $result['beneficiary_ta_signature_hash']],
                    ['field' => 'Beneficiary TA Signature', 'data'  => $result['beneficiary_ta_signature']],
                    ['field' => 'Crypto Address Type', 'data'  => $result['crypto_address_type']],
                    ['field' => 'Crypto Address', 'data' => $result['crypto_address']],
                    ['field' => 'Crypto Public Key', 'data'  => $result['crypto_public_key']],
                    ['field' => 'Crypto Signature Hash', 'data'  => $result['crypto_signature_hash']],
                    ['field' => 'Crypto Signature', 'data'  => $result['crypto_signature']],
                    ['field' => 'Sender TA Address', 'data'  => $result['sender_ta_address']],
                    ['field' => 'Sender TA Public Key', 'data' => $result['sender_ta_public_key']],
                    ['field' => 'Sender User Address', 'data'  => $result['sender_user_address']],
                    ['field' => 'Sender User Public Key', 'data'  => $result['sender_user_public_key']],
                    ['field' => 'Sender TA Signature Hash', 'data'  => $result['sender_ta_signature_hash']],
                    ['field' => 'Sender TA Signature', 'data'  => $result['sender_ta_signature']],
                    ['field' => 'Sender TA Signature', 'data'  => $result['sender_ta_signature']],
                    ['field' => 'Beneficiary KYC', 'data'  => $result['beneficiary_kyc']],
                    ['field' => 'Sender KYC', 'data'  => $result['sender_kyc']],
                    ['field' => 'Beneficiary KYC Decrypt', 'data'  => $result['beneficiary_kyc_decrypt']],
                    ['field' => 'Sender KYC Decrypt', 'data'  => $result['sender_kyc_decrypt']]
                  ];


        // return the current params and rows back
        return [
          'serverParams' => [
            'sort' => !empty($input['sort']) ? $input['sort'] : '',
            'page' => $page,
            'perPage' => $perPage,
          ],
          'totalRecords' => 13,
          'rows' => $list,
        ];
    }

}
