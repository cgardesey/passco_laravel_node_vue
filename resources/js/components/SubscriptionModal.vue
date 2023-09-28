<template>
    <modal name="subscription-modal"
           height="auto"
           :draggable="true"
           :adaptive="true"
           @before-close="beforeClose">
        <div class="container">

            <div class="col-md-12" style="margin-top: 20px">
                <form method="POST" action="/passco/logout">
                    <!--{{ csrf_field() }}-->
                    <input type="hidden" name="_token" id="csrf-token" :value="token" />
                    <button type="submit" class="btn btn-outline-primary float-right">Logout</button>
                </form>
            </div>
            <h1 align="center">Subscription</h1>
            <form class="form-horizontal" role="form"
                  @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                <div class="form-inner">
                    <input type="hidden" name="user_id" id="user_id" :value="user_id" />
                    <div class="form-group">
                        <div class="col-md-12" align="center">
                            <div style="margin-bottom: 10px" class="text-danger" v-if="form.errors.any()">Please correct
                                form errors.
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="name" class="control-label"> Name *</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="" v-model="form.name" required>
                            <small class="text-danger"
                                   v-if="form.errors.has('name')"
                                   v-text="form.errors.get('name')">
                            </small>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div><label class="control-label">Subscription Plan *</label></div>
                            <div class="radio">
                                <label><input type="radio" name="description" v-model="form.description" value="1 day @ GHC1.00" required> 1 day @ GHC1.00</label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="description" v-model="form.description" value="7 days @ GHC5.00"> 7 days @ GHC5.00</label>
                            </div>
                            <div class="radio ">
                                <label><input type="radio" name="description" v-model="form.description" value="30 days @ GHC5.00"> 30 days @ GHC5.00</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="mobileno" class="control-label">Mobile Number *</label>
                            <input type='text' class="form-control" id="mobileno" name="mobileno"
                                   placeholder="233 format" v-model="form.mobileno" required>
                            <small class="text-danger"
                                   v-if="form.errors.has('mobileno')"
                                   v-text="form.errors.get('mobileno')">
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="submit" value="Subscribe" class="btn btn-outline-primary float-right" style="margin-bottom: 20px"
                                   :disabled="form.errors.any()">
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </modal>
</template>

<script>
    import VModal from 'vue-js-modal';
    import Form from "../utilities/Form";
    import io from 'socket.io-client';
    import shared from "../shared";
    import {sha256} from "js-sha256";

    export default {
        extends: shared,
        components: {
            VModal
        },

        props: {
            token: String,
            name: String,
            user_id: Number,
            timeremaining: Number
        },

        data() {
            return{
                socket : io(':6999'),
                form: new Form(
                    {
                        user_id: '',
                        name: '',
                        description: '',
                        mobileno: ''
                    }),
                subscribed: false
            }
        },

        methods: {
            onSubmit() {
                this.form.post(BasePath + '/api/transactions')
                    .then(data => {
                        let token = sha256(data.paymentid + data.mobileno + data.chargeamount + 'test@123');
                        axios.post(TransactionApiPath, {
                            mobileno: data.mobileno,
                            customername: data.name,
                            description: data.description,
                            amount: data.chargeamount,
                            paymentid: data.paymentid,
                            token: token,
                        })
                            .then(response => {
                                console.log(response.data);
                            })
                            .catch(error => {
                                console.log(error.response.data.error);
                            });
                        /*axios.patch(BasePath + '/api/transactions/' + data.paymentid, {message: 'Sample message'})
                            .then(response => {
                                 // console.log(response.data);
                            })
                            .catch(error => {
                                console.log(error.response.data.error);
                            });*/
                    })
                    .catch(errors =>  {
                        console.log(errors);
                        // alert('Please correct form errors.');
                        window.scrollTo(0, 0);
                    })
            },

            beforeClose (event) {
                if (!this.subscribed) {
                    event.stop()
                }
                else {
                    this.resumeTimer();
                }
            }
        },

        mounted() {
            // console.log('timeremaining', this.timeremaining);
            this.form.name = this.name;
            this.form.user_id = this.user_id;

            this.socket.on('connect', function () {

                this.socket.emit("user-connected", {'user_id': this.user_id});

                if (this.timeremaining > 0) {
                    this.$modal.hide('subscription-modal');
                    this.resumeTimer();
                }
                else {
                    this.$modal.show('subscription-modal');
                }
            }.bind(this));
            this.socket.on('transaction-response_' + this.user_id, function (message) {
                console.log('transaction response', message);
                // message = JSON.parse(message);
                // // console.log(message);
                if (true) {
                    this.subscribed = true;
                    this.$modal.hide('subscription-modal');
                }
            }.bind(this));
            this.socket.on('no-time_' + this.user_id.toString(), function () {
                this.$modal.show('subscription-modal');
            }.bind(this));
        }
    }
</script>
