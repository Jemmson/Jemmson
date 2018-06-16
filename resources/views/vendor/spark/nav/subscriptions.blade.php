@if (Auth::user()->onTrial())
    <!-- Trial Reminder -->
    <span><a href="/settings#/subscription">
            <i class="fa fa-fw fa-btn fa-shopping-bag"></i>Subscribe
        </a></span>
@endif

@if (Spark::usesTeams() && Auth::user()->currentTeamOnTrial())
    <!-- Team Trial Reminder -->
    <span><a href="/settings/{{ str_plural(Spark::teamString()) }}/{{ Auth::user()->currentTeam()->id }}#/subscription">
            <i class="fa fa-fw fa-btn fa-shopping-bag"></i>Subscribe
        </a></span>
@endif
