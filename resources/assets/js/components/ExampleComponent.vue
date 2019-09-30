<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Ex222ample Component</div>

                    <div class="panel-body">
                        I'm an example component!
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
        Echo.join(`chat`)
            .here(users => {
                console.log(users);
            })
            .joining(user => {
                console.log(user.name);
            })
            .leaving(user => {
                console.log(user.name);
            });
            }
        }

</script>
