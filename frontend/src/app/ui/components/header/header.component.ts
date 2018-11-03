import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.sass']
})
export class HeaderComponent implements OnInit {
  public logo = 'asset/logo.svg';
  public title = 'Test 1';
  public liks = [
    {
      label: 'Posts',
      url: '/posts'
    },
    {
      label: 'Users',
      url: '/users'
    }
    ];
  constructor() { }

  ngOnInit() {
  }

}
