<!-- NavBar For Authenticated Users -->
<spark-navbar
        :user="user"
        :teams="teams"
        :current-team="currentTeam"
        :has-unread-notifications="hasUnreadNotifications"
        :has-unread-announcements="hasUnreadAnnouncements"
        inline-template>

    <div>
        <div v-if="user" class="flex items-center jemmson-navbar text-center">
            @if (Auth::user()->email == 'jemmsoninc@gmail.com')
                    <!-- Feedback -->
                    <a href="/#/feedback" class="flex-1 text-white">
                        <i class="fa fa-fw fa-btn fa-sign-out"></i>
                        <br>
                        Feedback
                    </a>           
             @endif
                <a @click="showNotifications" class="has-activity-indicator flex-1 text-white">
                    <!-- <i class="activity-indicator" v-if="hasUnreadNotifications || hasUnreadAnnouncements"></i> -->
                    <i class="icon fas fa-bell bell"></i>
                    <br>
                    Alerts
                </a>

                <a href="/settings" class="flex-1 text-white" v-if="user.usertype === 'contractor'">
                    <i class="fas fa-fw fa-btn fa-cog"></i>
                    <br>
                    Settings
                </a>

        @if (Auth::user()->onTrial())
            <!-- Trial Reminder -->
                <a href="/settings#/subscription" class="flex-1 text-white">
                    <i class="fa fa-fw fa-btn fa-shopping-bag"></i>
                    <br>
                    Subscribe
                </a>
        @endif

        @if (Spark::usesTeams() && Auth::user()->currentTeamOnTrial())
            <!-- Team Trial Reminder -->
            <span>
                <a href="/settings/{{ str_plural(Spark::teamString()) }}/{{ Auth::user()->currentTeam()->id }}#/subscription">
                <i class="fa fa-fw fa-btn fa-shopping-bag"></i>Subscribe
            </a></span>
        @endif


        <a href="/logout" class="text-white flex-1">
            <i class="fas fa-fw fa-btn fa-sign-out-alt"></i>
            <br>
            Logout
        </a>

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

        <div v-if="user && this.$route.fullPath !== '/furtherInfo'">
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
