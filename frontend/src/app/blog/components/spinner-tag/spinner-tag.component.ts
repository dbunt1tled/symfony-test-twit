import {Component, Input, OnInit} from '@angular/core';

@Component({
  selector: 'app-spinner-tag',
  templateUrl: './spinner-tag.component.html',
  styleUrls: ['./spinner-tag.component.sass']
})
export class SpinnerTagComponent implements OnInit {

  constructor() { }
  @Input() message: string = '';
  ngOnInit() {
  }
}
