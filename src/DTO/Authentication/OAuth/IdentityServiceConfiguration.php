<?php

namespace CodebarAg\DocuWare\DTO\Authentication\OAuth;

use Illuminate\Support\Arr;

final class IdentityServiceConfiguration
{
    public static function make(array $data): self
    {
        return new self(
            issuer: Arr::get($data, 'issuer'),
            jwksUri: Arr::get($data, 'jwks_uri'),
            authorizationEndpoint: Arr::get($data, 'authorization_endpoint'),
            tokenEndpoint: Arr::get($data, 'token_endpoint'),
            userinfoEndpoint: Arr::get($data, 'userinfo_endpoint'),
            endSessionEndpoint: Arr::get($data, 'end_session_endpoint'),
            checkSessionIframe: Arr::get($data, 'check_session_iframe'),
            revocationEndpoint: Arr::get($data, 'revocation_endpoint'),
            introspectionEndpoint: Arr::get($data, 'introspection_endpoint'),
            deviceAuthorizationEndpoint: Arr::get($data, 'device_authorization_endpoint'),
            backchannelAuthenticationEndpoint: Arr::get($data, 'backchannel_authentication_endpoint'),
            pushedAuthorizationRequestEndpoint: Arr::get($data, 'pushed_authorization_request_endpoint'),
            requirePushedAuthorizationRequests: Arr::get($data, 'require_pushed_authorization_requests'),
            frontchannelLogoutSupported: Arr::get($data, 'frontchannel_logout_supported'),
            frontchannelLogoutSessionSupported: Arr::get($data, 'frontchannel_logout_session_supported'),
            backchannelLogoutSupported: Arr::get($data, 'backchannel_logout_supported'),
            backchannelLogoutSessionSupported: Arr::get($data, 'backchannel_logout_session_supported'),
            scopesSupported: Arr::get($data, 'scopes_supported', []),
            claimsSupported: Arr::get($data, 'claims_supported', []),
            grantTypesSupported: Arr::get($data, 'grant_types_supported', []),
            responseTypesSupported: Arr::get($data, 'response_types_supported', []),
            responseModesSupported: Arr::get($data, 'response_modes_supported', []),
            tokenEndpointAuthMethodsSupported: Arr::get($data, 'token_endpoint_auth_methods_supported', []),
            idTokenSigningAlgValuesSupported: Arr::get($data, 'id_token_signing_alg_values_supported', []),
            subjectTypesSupported: Arr::get($data, 'subject_types_supported', []),
            codeChallengeMethodsSupported: Arr::get($data, 'code_challenge_methods_supported', []),
            requestParameterSupported: Arr::get($data, 'request_parameter_supported'),
            requestObjectSigningAlgValuesSupported: Arr::get($data, 'request_object_signing_alg_values_supported', []),
            promptValuesSupported: Arr::get($data, 'prompt_values_supported', []),
            authorizationResponseIssParameterSupported: Arr::get($data, 'authorization_response_iss_parameter_supported'),
            backchannelTokenDeliveryModesSupported: Arr::get($data, 'backchannel_token_delivery_modes_supported', []),
            backchannelUserCodeParameterSupported: Arr::get($data, 'backchannel_user_code_parameter_supported'),
            dpopSigningAlgValuesSupported: Arr::get($data, 'dpop_signing_alg_values_supported', []),
            windowsAuthEndpoint: Arr::get($data, 'windows_auth_endpoint'),
        );
    }

    public function __construct(
        public ?string $issuer,
        public ?string $jwksUri,
        public ?string $authorizationEndpoint,
        public ?string $tokenEndpoint,
        public ?string $userinfoEndpoint,
        public ?string $endSessionEndpoint,
        public ?string $checkSessionIframe,
        public ?string $revocationEndpoint,
        public ?string $introspectionEndpoint,
        public ?string $deviceAuthorizationEndpoint,
        public ?string $backchannelAuthenticationEndpoint,
        public ?string $pushedAuthorizationRequestEndpoint,
        public ?bool $requirePushedAuthorizationRequests,
        public ?bool $frontchannelLogoutSupported,
        public ?bool $frontchannelLogoutSessionSupported,
        public ?bool $backchannelLogoutSupported,
        public ?bool $backchannelLogoutSessionSupported,
        public array $scopesSupported,
        public array $claimsSupported,
        public array $grantTypesSupported,
        public array $responseTypesSupported,
        public array $responseModesSupported,
        public array $tokenEndpointAuthMethodsSupported,
        public array $idTokenSigningAlgValuesSupported,
        public array $subjectTypesSupported,
        public array $codeChallengeMethodsSupported,
        public ?bool $requestParameterSupported,
        public array $requestObjectSigningAlgValuesSupported,
        public array $promptValuesSupported,
        public ?bool $authorizationResponseIssParameterSupported,
        public array $backchannelTokenDeliveryModesSupported,
        public ?bool $backchannelUserCodeParameterSupported,
        public array $dpopSigningAlgValuesSupported,
        public ?string $windowsAuthEndpoint,
    ) {}
}
