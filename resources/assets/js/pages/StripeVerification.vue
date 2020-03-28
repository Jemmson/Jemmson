<template>
    <v-container>

        <!-- https://stripe.com/docs/api/accounts/object -->

        <!--requirements.current_deadline
        timestamp The date the fields in currently_due must be collected by to keep payouts enabled for the account.
        These fields might block payouts sooner if the next threshold is reached
        before these fields are collected.-->

        <!--requirements.currently_due
        array containing strings
        The fields that need to be collected to keep the account enabled.
        If not collected by the current_deadline, these fields appear in past_due as well,
        and the account is disabled.-->

        <!--requirements.disabled_reason
        string
        If the account is disabled, this string describes why the account can’t create charges
        or receive payouts. Can be requirements.past_due, requirements.pending_verification,
        rejected.fraud, rejected.terms_of_service, rejected.listed, rejected.other, listed, under_review,
        or other.-->

        <!--requirements.errors
        array of hashes
        PREVIEW FEATURE
        The fields that need to be collected again because
        validation or verification failed for some reason.-->

        <!--requirements.eventually_due
        array containing strings
        The fields that need to be collected assuming all volume
        thresholds are reached. As they become required, these fields appear in
        currently_due as well, and the current_deadline is set.-->

        <!--requirements.past_due
        array containing strings
        The fields that weren’t collected by the current_deadline. These fields need to be
        collected to re-enable the account.-->

        <!--requirements.pending_verification
        array containing strings
        Fields that may become required depending on the results of verification or review.
        An empty array unless an asynchronous verification is pending. If verification fails,
        the fields in this array become required and move to currently_due or past_due.-->

    </v-container>

</template>

<script>
    export default {
        name: "StripeVerification"
    }
</script>

<style scoped>

</style>