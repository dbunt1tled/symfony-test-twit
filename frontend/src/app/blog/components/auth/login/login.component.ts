import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import {UserLogin} from '../../../models/auth/user-login';
import {AuthService} from '../../../../http/auth/auth.service';
import {FlashMessagesService} from 'angular2-flash-messages';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.sass']
})
export class LoginComponent implements OnInit {

  loginForm: FormGroup;
  submitted: boolean = false;
  constructor(
    private _fb: FormBuilder,
    private _authService: AuthService,
    private _flashMessage: FlashMessagesService,
  ) { }

  ngOnInit() {
    this.loginForm =  this._fb.group({
      username: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required]],
    });
  }
  get getField() { return this.loginForm.controls; }

  onSubmit() {
    this.submitted = true;
    if (this.loginForm.invalid) {
      return;
    }
    let valuesForm = this.loginForm.value;
    let user: UserLogin = {username: valuesForm.username, password: valuesForm.password};
    this._authService.login(user)
      .then(token => {
        this._authService.redirectToMain();
      }).catch(err => {
      this._flashMessage.show('Wrong login data',
        {cssClass: 'alert-danger', closeOnClick: true, showCloseBtn: true, timeout: 3000 });
        console.log('Wrong login data');
    });
  }


}
