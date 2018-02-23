<a class="navbar-brand" href="/home">
    <img id="user_logo" src="" style="height: 32px;">
</a>
<script>
    let img = document.getElementById('user_logo');
    if (Spark.state.user !== undefined && Spark.state.user !== null) {
        if (Spark.state.user.logo_url !== null) {
            img.src = Spark.state.user.logo_url;
        } else {
             img.src = "/img/mono-logo.png";
        }
    } else {
        img.src = "/img/mono-logo.png";
    }
</script>