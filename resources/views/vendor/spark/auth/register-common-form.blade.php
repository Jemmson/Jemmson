<div class="flex flex-col further-info-main text-center">
    <div class="main-header p-4 uppercase">
        Please enter your credentials
    </div>

    <div class="box border flex flex-col section">
        <div class="content">

            <!-- Name -->
            <div class="input-section" :class="{'has-error': registerForm.errors.has('name')}">
                <label class="j-label">Name</label>

                <div class="">
                    <input type="text" class="border input" name="name" v-model="registerForm.name" autofocus>

                    <span class="help-block" v-show="registerForm.errors.has('name')">
                @{{ registerForm.errors.get('name') }}
            </span>
                </div>
            </div>

            <!-- E-Mail Address -->
            <div class="input-section" :class="{'has-error': registerForm.errors.has('email')}">
                <label class="j-label">E-Mail Address</label>

                <div>
                    <input type="email" class="border input" name="email" v-model="registerForm.email">

                    <span class="help-block" v-show="registerForm.errors.has('email')">
                @{{ registerForm.errors.get('email') }}
            </span>
                </div>
            </div>

            <hr>

            <div class="flex flex-col">

                <h5 class="uppercase text-center j-label mb-4">who are you?</h5>

                <div class="flex justify-around">
                    <div class="input-section" :class="{'has-error': registerForm.errors.has('usertype')}">
                        <label class="j-label">Contractor</label>

                        <div>
                            <input type="radio" id="usertypeContractor" name="usertypeContractor" value="contractor"
                                   v-model="registerForm.usertype">
                            <span class="help-block" v-show="registerForm.errors.has('usertype')">
                @{{ registerForm.errors.get('usertype') }}
            </span>
                        </div>
                    </div>

                    <div class="input-section" :class="{'has-error': registerForm.errors.has('usertype')}">
                        <label class="j-label">Customer</label>

                        <div>
                            <input type="radio" name="usertype" value="customer" v-model="registerForm.usertype">
                            <span class="help-block" v-show="registerForm.errors.has('usertype')">
                @{{ registerForm.errors.get('usertype') }}
            </span>
                        </div>
                    </div>
                </div>

            </div>

            <hr>

            <!-- Password -->
            <div class="input-section" :class="{'has-error': registerForm.errors.has('password')}">
                <label class="j-label">Password</label>

                <div>
                    <input type="password" class="border input" name="password" v-model="registerForm.password">

                    <span class="help-block" v-show="registerForm.errors.has('password')">
                @{{ registerForm.errors.get('password') }}
            </span>
                </div>
            </div>

            <!-- Password Confirmation -->
            <div class="input-section" :class="{'has-error': registerForm.errors.has('password_confirmation')}">
                <label class="j-label">Confirm Password</label>

                <div>
                    <input type="password" class="border input" name="password_confirmation"
                           v-model="registerForm.password_confirmation" @keyup="confirmPassword">

                    <span class="help-block" v-show="registerForm.errors.has('password_confirmation')">
                @{{ registerForm.errors.get('password_confirmation') }}
            </span>
                </div>
            </div>

            <!-- Terms And Conditions -->
            <div v-if=" ! selectedPlan || selectedPlan.price == 0">
                <div class="input-section" :class="{'has-error': registerForm.errors.has('terms')}">
                    <div class="col-md-6 col-md-offset-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="terms" v-model="registerForm.terms">
                                I Accept The <a href="/terms" target="_blank">Terms Of Service</a>
                            </label>

                            <span class="help-block" v-show="registerForm.errors.has('terms')">
                        @{{ registerForm.errors.get('terms') }}
                    </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="input-section">
                <div class="col-md-6 col-md-offset-4">
                    <button id="register" name=register
                            class="register text-center border shadow uppercase"
                            @click.prevent="register"
                            :disabled="registerForm.busy">
                    <span v-if="registerForm.busy">
                        <i class="fa fa-btn fa-spinner fa-spin"></i>Registering
                    </span>

                        <span v-else>
                        <i class="fa fa-btn fa-check-circle mr-2"></i>Register
                    </span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <jem-footer></jem-footer>

</div>