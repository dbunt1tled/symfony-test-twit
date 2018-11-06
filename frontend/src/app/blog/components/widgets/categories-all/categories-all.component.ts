import { Component, OnInit } from '@angular/core';
import {BlogService} from '../../../services/blog.service';

@Component({
  selector: 'app-categories-all',
  templateUrl: './categories-all.component.html',
  styleUrls: ['./categories-all.component.sass']
})
export class CategoriesAllComponent implements OnInit {
  categories: any;
  loaded: boolean = false;
  constructor(
    private _blogService: BlogService,
  ) { }

  ngOnInit() {
    this.getCategories();
  }
  getCategories()
  {
    this._blogService.getCategoriesTreeAll().subscribe(categories => {
      this.categories = categories;
      this.loaded = true;
    })
  }
}
