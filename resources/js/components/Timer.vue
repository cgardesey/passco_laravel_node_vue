<template>
    <div style="color: green; width: 100%" align="center" class="mb-3">
        {{ time}}
        <!--<button type="button" class="btn btn-outline-primary" style="margin-left: 10px" @click="pause">{{ btnText }}</button>-->
    </div>
</template>

<script>
    import io from 'socket.io-client';
    import shared from "../shared";

    export default {
        extends: shared,

        props: ['user_id'],

        data() {
            return{
                socket : io(':6999'),
                time: '00d 00h 00m 00s',
                btnText: 'Pause'
            }
        },

        methods: {
            pause() {
                if (this.btnText === 'Pause') {
                    this.btnText = 'Resume';
                    this.stopTimer();
                }
                else {
                    this.btnText = 'Pause';
                    this.resumeTimer();
                }
            },

            stopTimer() {
                this.socket.emit("stop-timer", {'user_id': this.user_id});
            },

            formatTime(secs) {
                let seconds = parseInt(secs, 10);
                let days = Math.floor(seconds / (3600 * 24));
                seconds  -= days*3600*24;
                let hrs   = Math.floor(seconds / 3600);
                seconds  -= hrs*3600;
                let mnts = Math.floor(seconds / 60);
                seconds  -= mnts*60;
                return days + 'd ' + hrs + 'h ' +mnts+'m ' + seconds + 's';
            }
        },

        mounted() {
            this.socket.on(this.user_id.toString(), function (sec_remaining) {
                this.time = this.formatTime(sec_remaining);
            }.bind(this));
        }
    }
</script>
