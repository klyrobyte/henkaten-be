# Authenticating requests

To authenticate requests, include a **`X-App-Secret`** header with the value **`"{HMAC_SHA256_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

For service clients, provide the SHA-256 HMAC of "henkaten-api" using your APP_API_SECRET.
