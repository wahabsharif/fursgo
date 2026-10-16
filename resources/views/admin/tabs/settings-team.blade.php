@php
    $settingsSections = [
        ['id' => 'team', 'label' => 'Team Members'],
        ['id' => 'platform', 'label' => 'Platform Settings'],
        ['id' => 'general', 'label' => 'General Settings'],
    ];

    $teamMembers = [
        [
            'initials' => 'MM',
            'name' => 'Michelle M',
            'email' => 'michelle@fursgo.com',
            'role' => 'Super Admin',
            'roleClass' => 'super',
            'activity' => 'Last login Today, 08:14 · Last active 2 hours ago · 2 open disputes',
            'status' => 'Active',
        ],
        [
            'initials' => 'BM',
            'name' => 'Ben M',
            'email' => 'ben@fursgo.com',
            'role' => 'Super Admin',
            'roleClass' => 'super',
            'activity' => 'Last login Just now · Last active Just now',
            'status' => 'Active',
        ],
        [
            'initials' => 'PK',
            'name' => 'Penelope K',
            'email' => 'penelope@fursgo.com',
            'role' => 'Support Admin',
            'roleClass' => 'support',
            'activity' => 'Last login Yesterday, 17:40 · Last active Yesterday',
            'status' => 'Active',
        ],
        [
            'initials' => 'RU',
            'name' => 'Rupert U',
            'email' => 'Rupert@fursgo.com',
            'role' => 'Content Editor',
            'roleClass' => 'content',
            'activity' => 'Last login — · Last active —',
            'status' => 'Invited',
        ],
    ];

    $roles = [
        [
            'name' => 'Super Admin',
            'class' => 'super',
            'can' => [
                'Platform settings (fees, payout schedules)',
                'Delete any user or provider account',
                'Publish legal pages + force reconfirmation',
                'Approve / fail verifications',
                'Hold or release any payout',
                'Manage admin team (invite, revoke, change roles)',
                "View FursGo's own revenue and settlement data",
                'Assign or reassign disputes to any team member',
                'View full admin activity log',
            ],
            'cannot' => [],
            'note' => 'No restrictions — full access to every admin section.',
        ],
        [
            'name' => 'Support Admin',
            'class' => 'support',
            'can' => [
                'View customer and provider accounts (read only)',
                'Add internal notes to accounts and bookings',
                'View booking detail (read only)',
                'View flagged reviews queue',
                'Claim and work on assigned disputes',
            ],
            'cannot' => [
                'Change any account status (suspend/delete)',
                'Issue refunds or hold payouts',
                'Approve/fail verifications',
                'Access platform settings or financial data',
                'Access content manager',
            ],
        ],
        [
            'name' => 'Content Editor',
            'class' => 'content',
            'can' => [
                'Edit all marketing page content (both sites)',
                'Edit email templates',
                'Create and edit help articles and FAQs',
                'Save drafts',
            ],
            'cannot' => [
                'Publish legal pages (super admin only)',
                'Trigger re-confirmation on policy pages',
                'Access any user, booking or financial data',
                'Access platform settings',
            ],
        ],
    ];
@endphp

<div class="admin-settings-team" x-data="{ section: 'team', automationOn: true, maintenanceOn: false }">
    <div class="admin-wc-separator" aria-hidden="true"></div>

    <nav class="admin-po-sections admin-settings-sections" aria-label="Settings sections">
        @foreach ($settingsSections as $item)
            <button
                type="button"
                class="admin-po-section"
                :class="{ 'is-active': section === @js($item['id']) }"
                @click="section = @js($item['id'])">
                {{ $item['label'] }}
            </button>
        @endforeach
    </nav>

    <div class="admin-settings-panel" x-show="section === 'team'" x-cloak>
        <div class="admin-settings-heading">
            <h1 class="admin-page-title">Team Members</h1>
            <button type="button" class="admin-btn-dark admin-team-invite">+ Invite Team Member</button>
        </div>

        <section class="admin-team-list-card" aria-labelledby="team-list-title">
            <div class="admin-team-list-head">
                <h2 id="team-list-title">All Members</h2>
                <p>4 Active · 1 pending invite</p>
            </div>

            <div class="admin-team-members">
                @foreach ($teamMembers as $member)
                    <article class="admin-team-member">
                        <span class="admin-team-avatar" aria-hidden="true">{{ $member['initials'] }}</span>

                        <div class="admin-team-member-copy">
                            <div class="admin-team-identity">
                                <div>
                                    <h3>{{ $member['name'] }}</h3>
                                    <p>{{ $member['email'] }}</p>
                                </div>
                                <span class="admin-role-chip is-{{ $member['roleClass'] }}">{{ $member['role'] }}</span>
                            </div>
                            <p class="admin-team-activity">{{ $member['activity'] }}</p>
                        </div>

                        <div class="admin-team-actions">
                            <button type="button" class="admin-team-action" aria-label="Edit {{ $member['name'] }}">
                                <svg viewBox="0 0 18 18" aria-hidden="true"><path d="M11.25 3.25 14.75 6.75M4 14l1-4L12.6 2.4a1.5 1.5 0 0 1 2.1 0l.9.9a1.5 1.5 0 0 1 0 2.1L8 13l-4 1Z"/></svg>
                            </button>
                            <button type="button" class="admin-team-action" aria-label="View {{ $member['name'] }}">
                                <svg viewBox="0 0 18 18" aria-hidden="true"><path d="M1.5 9s2.75-4 7.5-4 7.5 4 7.5 4-2.75 4-7.5 4-7.5-4-7.5-4Z"/><circle cx="9" cy="9" r="2"/></svg>
                            </button>
                            <button type="button" class="admin-team-action" aria-label="Remove {{ $member['name'] }}">
                                <svg viewBox="0 0 18 18" aria-hidden="true"><path d="m4 4 10 10M14 4 4 14"/></svg>
                            </button>
                            <span class="admin-team-status {{ $member['status'] === 'Active' ? 'is-active' : 'is-invited' }}">{{ $member['status'] }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="admin-roles" aria-labelledby="roles-title">
            <div class="admin-roles-head">
                <h2 id="roles-title">Roles &amp; Permissions</h2>
                <p>Fixed permission sets — assign a role to a person in Team Members</p>
            </div>

            <div class="admin-role-grid">
                @foreach ($roles as $role)
                    <article class="admin-role-card">
                        <span class="admin-role-chip is-{{ $role['class'] }}">{{ $role['name'] }}</span>

                        <div class="admin-role-block">
                            <h3>CAN DO</h3>
                            <ul>
                                @foreach ($role['can'] as $permission)
                                    <li class="is-allowed"><span aria-hidden="true">✓</span>{{ $permission }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="admin-role-block admin-role-cannot">
                            <h3>CANNOT DO</h3>
                            @if (!empty($role['note']))
                                <p>{{ $role['note'] }}</p>
                            @else
                                <ul>
                                    @foreach ($role['cannot'] as $permission)
                                        <li class="is-denied"><span aria-hidden="true">×</span>{{ $permission }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>

    <div class="admin-platform-settings" x-show="section === 'platform'" x-cloak>
        <h1 class="admin-page-title">Platform Settings</h1>

        <div class="admin-platform-automation">
            <div class="admin-platform-automation-copy">
                <span class="admin-platform-automation-state" :class="{ 'is-off': !automationOn }">
                    <span class="admin-platform-dot" aria-hidden="true"></span>
                    <span x-text="automationOn ? 'Automation On' : 'Automation Off'"></span>
                </span>
                <span class="admin-platform-next-run" x-show="automationOn">Next run: Friday, 18:00 GMT (weekly)</span>
            </div>
            <button
                type="button"
                class="admin-platform-text-btn"
                @click="automationOn = !automationOn"
                x-text="automationOn ? 'Turn Off' : 'Turn On'">
            </button>
        </div>

        <div class="admin-platform-grid">
            <section class="admin-platform-card" aria-labelledby="payout-schedule-title">
                <div class="admin-platform-card-head">
                    <h2 id="payout-schedule-title">Payout Schedule</h2>
                    <button type="button" class="admin-platform-edit-btn" aria-label="Edit payout schedule">
                        <svg viewBox="0 0 18 18" aria-hidden="true"><path d="M11.25 3.25 14.75 6.75M4 14l1-4L12.6 2.4a1.5 1.5 0 0 1 2.1 0l.9.9a1.5 1.5 0 0 1 0 2.1L8 13l-4 1Z"/></svg>
                        Edit
                    </button>
                </div>
                <dl class="admin-platform-values">
                    <div><dt>Frequency</dt><dd>Weekly</dd></div>
                    <div><dt>Day</dt><dd>Friday</dd></div>
                    <div><dt>Cash-out cut-off time</dt><dd>18:00 GMT</dd></div>
                    <div><dt>Minimum payout threshold</dt><dd>&pound;10.00</dd></div>
                </dl>
            </section>

            <section class="admin-platform-card" aria-labelledby="referral-rewards-title">
                <div class="admin-platform-card-head">
                    <h2 id="referral-rewards-title">Referral Rewards</h2>
                    <button type="button" class="admin-platform-edit-btn" aria-label="Edit referral rewards">
                        <svg viewBox="0 0 18 18" aria-hidden="true"><path d="M11.25 3.25 14.75 6.75M4 14l1-4L12.6 2.4a1.5 1.5 0 0 1 2.1 0l.9.9a1.5 1.5 0 0 1 0 2.1L8 13l-4 1Z"/></svg>
                        Edit
                    </button>
                </div>
                <dl class="admin-platform-values">
                    <div><dt>Customer referral reward</dt><dd>&pound;10 credit</dd></div>
                    <div><dt>Provider referral reward</dt><dd>&pound;20 credit</dd></div>
                    <div><dt>Referral credit expiry</dt><dd>180 days</dd></div>
                </dl>
            </section>

            <section class="admin-platform-card" aria-labelledby="failure-handling-title">
                <div class="admin-platform-card-head">
                    <h2 id="failure-handling-title">Failure Handling &amp; Notifications</h2>
                    <button type="button" class="admin-platform-edit-btn" aria-label="Edit failure handling and notifications">
                        <svg viewBox="0 0 18 18" aria-hidden="true"><path d="M11.25 3.25 14.75 6.75M4 14l1-4L12.6 2.4a1.5 1.5 0 0 1 2.1 0l.9.9a1.5 1.5 0 0 1 0 2.1L8 13l-4 1Z"/></svg>
                        Edit
                    </button>
                </div>
                <dl class="admin-platform-values">
                    <div><dt>Retry attempts</dt><dd>3</dd></div>
                    <div><dt>Retry interval</dt><dd>24 hours</dd></div>
                    <div><dt>Notify providers on failure</dt><dd>ON</dd></div>
                    <div><dt>Notify admin team on failure</dt><dd>ON</dd></div>
                </dl>
            </section>

            <section class="admin-platform-card" aria-labelledby="automation-runs-title">
                <div class="admin-platform-card-head">
                    <h2 id="automation-runs-title">Recent Automation Runs</h2>
                    <button type="button" class="admin-platform-text-btn">View all runs</button>
                </div>
                <ul class="admin-platform-runs">
                    <li>
                        <span class="admin-platform-run-date"><span aria-hidden="true">&bull;</span> 14 Aug 2024</span>
                        <span>214 providers &middot; &pound;38,240.00</span>
                        <span class="admin-platform-run-status is-complete">Completed</span>
                    </li>
                    <li>
                        <span class="admin-platform-run-date"><span aria-hidden="true">&bull;</span> 31 Jul 2024</span>
                        <span>209 providers &middot; &pound;36,890.00</span>
                        <span class="admin-platform-run-status is-complete">Completed</span>
                    </li>
                    <li>
                        <span class="admin-platform-run-date"><span aria-hidden="true">&bull;</span> 17 Jul 2024</span>
                        <span>207 providers &middot; &pound;35,110.00</span>
                        <span class="admin-platform-run-status is-complete">Completed</span>
                    </li>
                    <li>
                        <span class="admin-platform-run-date"><span aria-hidden="true">&bull;</span> 3 Jul 2024</span>
                        <span>214 providers &middot; &pound;38,240.00</span>
                        <span class="admin-platform-run-status is-warning">Completed with 2 failures</span>
                    </li>
                </ul>
            </section>
        </div>

        <section class="admin-platform-history" aria-labelledby="platform-history-title">
            <div class="admin-platform-history-head">
                <h2 id="platform-history-title">Version History</h2>
                <button type="button" class="admin-platform-edit-btn admin-platform-full-log">
                    <span aria-hidden="true">&#8599;</span>
                    Full Log
                </button>
            </div>
            <ul>
                <li>
                    <span class="admin-platform-history-dot" aria-hidden="true"></span>
                    <div>
                        <p>Michelle M changed Minimum payout threshold: &pound;5.00 &rarr; &pound;10.00</p>
                        <time datetime="2025-04-18T14:32:00">18 Apr 2025 &middot; 14:32</time>
                    </div>
                </li>
                <li>
                    <span class="admin-platform-history-dot" aria-hidden="true"></span>
                    <div>
                        <p>Ben M changed Payout day: Thursday &rarr; Friday</p>
                        <time datetime="2025-04-18T11:32:00">18 Apr 2025 &middot; 11:32</time>
                    </div>
                </li>
            </ul>
        </section>
    </div>

    <div class="admin-general-settings" x-show="section === 'general'" x-cloak>
        <h1 class="admin-page-title">General Settings</h1>

        <section class="admin-general-card" aria-labelledby="general-payout-title">
            <div class="admin-general-card-head">
                <h2 id="general-payout-title">Payout Schedule</h2>
                <button type="button" class="admin-platform-edit-btn" aria-label="Edit payout schedule">
                    <svg viewBox="0 0 18 18" aria-hidden="true"><path d="M11.25 3.25 14.75 6.75M4 14l1-4L12.6 2.4a1.5 1.5 0 0 1 2.1 0l.9.9a1.5 1.5 0 0 1 0 2.1L8 13l-4 1Z"/></svg>
                    Edit
                </button>
            </div>
            <dl class="admin-platform-values">
                <div><dt>Legal business name</dt><dd>FursGo</dd></div>
                <div><dt>Public support email</dt><dd>fursgo@gmail.com</dd></div>
                <div><dt>Admin alerts email</dt><dd>admin@fursgo.com</dd></div>
            </dl>
        </section>

        <section class="admin-general-card" aria-labelledby="regional-settings-title">
            <div class="admin-general-card-head">
                <h2 id="regional-settings-title">Regional Settings</h2>
                <button type="button" class="admin-platform-edit-btn" aria-label="Edit regional settings">
                    <svg viewBox="0 0 18 18" aria-hidden="true"><path d="M11.25 3.25 14.75 6.75M4 14l1-4L12.6 2.4a1.5 1.5 0 0 1 2.1 0l.9.9a1.5 1.5 0 0 1 0 2.1L8 13l-4 1Z"/></svg>
                    Edit
                </button>
            </div>
            <dl class="admin-platform-values">
                <div><dt>Currency</dt><dd>GBP (&pound;)</dd></div>
                <div><dt>Timezone</dt><dd>Europe/London (GMT)</dd></div>
                <div><dt>Date format</dt><dd>DD/MM/YYYY</dd></div>
            </dl>
        </section>

        <section class="admin-general-maintenance" :class="{ 'is-active': maintenanceOn }" aria-labelledby="maintenance-mode-title">
            <div>
                <h2 id="maintenance-mode-title">Maintenance Mode</h2>
                <p>Takes the customer &amp; business sites offline with a holding page</p>
            </div>
            <button
                type="button"
                class="admin-general-maintenance-btn"
                :class="{ 'is-active': maintenanceOn }"
                :aria-pressed="maintenanceOn.toString()"
                @click="maintenanceOn = !maintenanceOn"
                x-text="maintenanceOn ? 'Turn off maintenance mode' : 'Turn on maintenance mode'">
            </button>
        </section>
    </div>
</div>
