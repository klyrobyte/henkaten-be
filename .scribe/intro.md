# Introduction



<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>

    ## Henkaten Board Secure API
    
    This API is protected via dual-layered HMAC and Session Nonce security.
    - **Browser Clients**: Handled automatically via session nonce.
    - **Service Clients**: Include the `X-App-Secret` header containing `hash_hmac('sha256', 'henkaten-api', APP_API_SECRET)`.

