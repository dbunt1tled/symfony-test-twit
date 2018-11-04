import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-footer',
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.sass']
})
export class FooterComponent implements OnInit {
  public dateNow;
  constructor() { }

  ngOnInit() {
    this.dateNow = new Date();
  }

}
