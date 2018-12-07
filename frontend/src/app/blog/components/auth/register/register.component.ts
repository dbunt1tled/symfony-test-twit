import { Component, OnInit } from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {AuthService} from '../../../../http/auth/auth.service';
import {UserRegister} from '../../../models/auth/user-register';
import {Status} from '../../../models/common/status';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.sass']
})
export class RegisterComponent implements OnInit {
  minSymbols = 3;
  registerForm: FormGroup;
  submitted = false;
  constructor(
    private _fb: FormBuilder,
    private _authService: AuthService,
  ) { }

  ngOnInit() {
    this.registerForm = this._fb.group({
      firstName: ['', [Validators.required, Validators.minLength(this.minSymbols)]],
      lastName: ['', [Validators.required, Validators.minLength(this.minSymbols)]],
      email: ['', [Validators.required, Validators.email]],
      plainPassword: ['', [Validators.required, Validators.minLength(this.minSymbols)]],
      confirmPassword: ['', [Validators.required]],
    }, {validator: this.checkPasswords});
  }

  get getField() { return this.registerForm.controls; }

  checkPasswords (group: FormGroup) {
    let valuesForm = group.value;
    const plainPassword = valuesForm.plainPassword;
    const confirmPassword = valuesForm.confirmPassword;
    return plainPassword === confirmPassword ? null : {notSame: true}
  }
  onSubmit() {
    this.submitted = true;
    if (this.registerForm.invalid) {
      return false;
    }
    const valuesForm = this.registerForm.value;
    const user: UserRegister = {
      email: valuesForm.email,
      firstName: valuesForm.firstName,
      lastName: valuesForm.lastName,
      plainPassword: valuesForm.plainPassword,
    };
    this._authService.register(user)
      .then((status: Status) => {
        if (status.status) {
          this._authService.redirectToLogin();
        } else {
          if (typeof status.message === 'object') {
            for (const index in status.message) {
              if (this.registerForm.controls[index]) {
                this.registerForm.controls[index].setErrors({'incorrect': status.message[index]});
              } else {
                console.log('Error: ' + index + ' (' + status.message[index] + ')');
              }
            }
          }
          if(typeof status.message === 'string') {
            console.log('Error: ' + status.message);
          }
          return false;
        }
      })
      .catch(error => {
        console.log(error);
        return false;
      });
  }
}
