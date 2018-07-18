<!-- NavBar For Authenticated Users -->
<spark-navbar
        :user="user"
        :teams="teams"
        :current-team="currentTeam"
        :has-unread-notifications="hasUnreadNotifications"
        :has-unread-announcements="hasUnreadAnnouncements"
        inline-template>

    <div>
        <div v-if="user" class="flex jemmson-navbar text-center">
            <div class="flex-1">
                <a @click="showNotifications" class="has-activity-indicator">
                <div class="navbar-icon">
                    <button>
                        <!-- <i class="activity-indicator" v-if="hasUnreadNotifications || hasUnreadAnnouncements"></i> -->
                        <i class="icon fas fa-bell bell"></i>
                        <br>
                        Alerts
                    </button>
                </div>
            </a>
            </div>

            <div class="flex-1" v-if="user.usertype === 'contractor'">
                <a href="/settings">
                    <i class="fas fa-fw fa-btn fa-cog"></i>
                    <br>
                    Settings
                </a>
            </div>

        @if (Auth::user()->onTrial())
            <!-- Trial Reminder -->
            <div class="flex-1">
                <a href="/settings#/subscription">
                    <i class="fa fa-fw fa-btn fa-shopping-bag"></i>
                    <br>
                    Subscribe
                </a>
            </div>
        @endif

        @if (Spark::usesTeams() && Auth::user()->currentTeamOnTrial())
            <!-- Team Trial Reminder -->
            <span><a href="/settings/{{ str_plural(Spark::teamString()) }}/{{ Auth::user()->currentTeam()->id }}#/subscription">
                <i class="fa fa-fw fa-btn fa-shopping-bag"></i>Subscribe
            </a></span>
        @endif


            <div class="flex-1">
                <a href="/logout">
                    <i class="fas fa-fw fa-btn fa-sign-out-alt"></i>
                    <br>
                    Logout
                </a>
            </div>

        @if (session('spark:impersonator'))
            <!--<li class="dropdown-header">Impersonation</li> -->

                <!-- Stop Impersonating -->
                <span><a href="/spark/kiosk/users/stop-impersonating">
                    <i class="fas fa-fw fa-btn fa-user-secret"></i>Back To My Account
                </a></span>

        @endif

        @if (Spark::developer(Auth::user()->email))
            <div class="flex-1">
                @include('spark::nav.developer')
            </div>
        @endif


        @if (Spark::usesTeams() && (Spark::createsAdditionalTeams() || Spark::showsTeamSwitcher()))
            <!-- Team Settings -->
            <div class="flex-1">
                @include('spark::nav.teams')
            </div>
        @endif

        @if (Spark::hasSupportAddress())
            <!-- Support -->
            <div class="flex-1">
                @include('spark::nav.support')
            </div>
        @endif

        </div>

        <div v-if="user">
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
