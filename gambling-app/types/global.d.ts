interface Tag {
  _id: string;
  name: string;
}
interface Author {
  _id: string;
  name: string;
  image: string;
}
export interface Question {
  _id: string;
  title: string;
  tags: Tag[];
  author: Author;
  upvoetes: number;
  answers: number;
  views: number;
  createdAt: Date;
}