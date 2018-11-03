import {NgModule} from '@angular/core';
import {Routes, RouterModule} from '@angular/router';
import {PostsComponent} from './components/posts/posts.component';

const routes: Routes = [
  {
    path: '',
    component: PostsComponent,
    // resolve: {}
  },
  {
    path: ':slug',
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BlogRoutingModule {
}
