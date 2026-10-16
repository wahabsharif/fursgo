<div class="admin-account-page">
    <div class="admin-wc-separator" aria-hidden="true"></div>
    <h1 class="admin-page-title">My Profile</h1>

    <section class="admin-profile-card admin-profile-main-card">
        <div class="admin-profile-identity">
            <span class="admin-team-avatar" aria-hidden="true">MM</span>
            <div>
                <h2>Michelle M</h2>
                <p>michelle@fursgo.com</p>
            </div>
            <span class="admin-role-chip is-super">Super Admin</span>
            <button type="button" class="admin-platform-edit-btn admin-profile-edit">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                    <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>Edit
            </button>
        </div>
        <dl class="admin-platform-values admin-profile-values">
            <div>
                <dt>Full name</dt>
                <dd>Name Surname</dd>
            </div>
            <div>
                <dt>Email</dt>
                <dd>name@fursgo.com</dd>
            </div>
            <div>
                <dt>Language</dt>
                <dd>English (UK)</dd>
            </div>
        </dl>
    </section>

    <section class="admin-profile-card admin-profile-setting-card">
        <div>
            <h2>Password</h2>
            <p>Last changed 3 months ago</p>
        </div>
        <button type="button" class="admin-profile-pill-btn">Change password</button>
    </section>

    @foreach ([1, 2] as $twoFactorCard)
    <section class="admin-profile-card admin-profile-security-card">
        <div class="admin-profile-setting-head">
            <div>
                <h2>Two-Factor Authentication</h2>
                <p>Enhance security with 2FA</p>
            </div>
            <button type="button" class="admin-profile-pill-btn">Turn off 2FA</button>
        </div>
        <dl class="admin-platform-values">
            <div>
                <dt>Email</dt>
                <dd class="is-success">Active</dd>
            </div>
            <div>
                <dt>Mobile app authenticator</dt>
                <dd class="is-muted">Not set up</dd>
            </div>
        </dl>
    </section>
    @endforeach

    <section class="admin-profile-card admin-profile-sessions">
        <div class="admin-profile-setting-head">
            <div>
                <h2>Active Sessions</h2>
                <p>3 signed-in devices</p>
            </div>
            <button type="button" class="admin-profile-pill-btn">Revoke all</button>
        </div>
        <div class="admin-profile-session-list">
            <article>
                <div>
                    <h3>Chrome on macOS <span>&middot; Current session</span></h3>
                    <p>London, UK &middot; Last active Just now</p>
                </div>
            </article>
            <article>
                <div>
                    <h3>Safari on iPhone</h3>
                    <p>London, UK &middot; Last active 2 hours ago</p>
                </div>
                <button type="button">Sign Out</button>
            </article>
            <article>
                <div>
                    <h3>Chrome on Windows</h3>
                    <p>Manchester, UK &middot; Last active 5 days ago</p>
                </div>
                <button type="button">Sign Out</button>
            </article>
        </div>
    </section>
</div>