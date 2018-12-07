import {Post} from './post';
import {User} from './user';

export interface UserPosts {
  posts: Post[]|null;
  user: User;
}
