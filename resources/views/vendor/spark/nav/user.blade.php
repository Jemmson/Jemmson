<!-- NavBar For Authenticated Users -->
<spark-navbar
        :user="user"
        :teams="teams"
        :current-team="currentTeam"
        :has-unread-notifications="hasUnreadNotifications"
        :has-unread-announcements="hasUnreadAnnouncements"
        inline-template>

    <div>
        <div v-if="user" class="jemmson-navbar">
            <a @click="showNotifications" class="has-activity-indicator">
                <div class="navbar-icon"><button>
                    <i class="activity-indicator" v-if="hasUnreadNotifications || hasUnreadAnnouncements"></i>
                    <i class="icon fas fa-bell"></i>
                    </button></div>
            </a>

        @if (session('spark:impersonator'))
            <!--<li class="dropdown-header">Impersonation</li> -->

                <!-- Stop Impersonating -->
                <span><a href="/spark/kiosk/users/stop-impersonating">
                    <i class="fas fa-fw fa-btn fa-user-secret"></i>Back To My Account
                </a></span>

            @endif

            @if (Spark::developer(Auth::user()->email))
                @include('spark::nav.developer')
            @endif

            @include('spark::nav.subscriptions')

            <span><a href="/settings">
                <i class="fas fa-fw fa-btn fa-cog"></i>Your Settings
            </a></span>

        @if (Spark::usesTeams() && (Spark::createsAdditionalTeams() || Spark::showsTeamSwitcher()))
            <!-- Team Settings -->
            @include('spark::nav.teams')
        @endif

        @if (Spark::hasSupportAddress())
            <!-- Support -->
            @include('spark::nav.support')
        @endif

        <!-- Logout -->
                    <span><a href="/logout">
                <i class="fas fa-fw fa-btn fa-sign-out-alt"></i>Logout
            </a></span>

        </div>

        <div v-if="user" class="jemmson-footer">
            @if (Auth::user()->usertype === 'contractor')
                @include('spark::nav.contractor-left')
            @endif
            @if (Auth::user()->usertype === 'customer')
                @include('spark::nav.customer-left')
            @endif
            @includeIf('spark::nav.user-right')
        </div>

    </div>

</spark-navbar>
