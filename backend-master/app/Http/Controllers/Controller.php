<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function wrapInnerApiResult($status, $result = null) {
        if (is_array($status)) {
            [$status, $result] = $status;
        }

        if ($status == "ok") {
            return [
                "status" => "ok",
                "result" => $result
            ];
        } else {
            return [
                "status" => "bad",
                "reason" => $status
            ];
        }
    }

    public function result($status, $result = null) {
        return $this->wrapInnerApiResult($status, $result);
    }

    public function okResponse($result = null) {
        return $this->result("ok", $result);
    }

    public function badResponse($reason) {
        return $this->result($reason);
    }

    public function validate(Request $request, array $rules,
                             array $messages = [], array $customAttributes = []
    ) {
        try {
            $validator = $this->getValidationFactory()
                ->make($request->all(), $rules, $messages, $customAttributes);
            $validator->getTranslator()->setLocale('validation_error_signatures');
            $validator->validate();

            return null;
        } catch (\Illuminate\Validation\ValidationException $exception) {
            stop(422, [
                "status" => "bad",
                "reason" => "validation_failed",
                "reason_extra" => [
                    "failed_fields" => array_keys($exception->validator->errors()->all()),
                    "fails_reason" => $exception->validator->errors(),
                    "message" => $exception->getMessage()
                ]
            ]);
        }
    }
}
