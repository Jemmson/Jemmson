<template>
    <a class="navbar-brand" href="/home">
        <img id="user_logo" :src="url" style="height: 32px;">
    </a>
</template>

<script>
export default {
    computed: {
        url() {
            if (Spark.state.user !== undefined && Spark.state.user !== null) {
                if (Spark.state.user.logo_url !== null) {
                    return Spark.state.user.logo_url;
                } else {
                    return "/img/mono-logo.png";
                }
            } else {
                return "/img/mono-logo.png";
            }
        }
    }
}
</script>