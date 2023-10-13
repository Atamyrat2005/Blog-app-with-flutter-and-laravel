// ignore_for_file: library_private_types_in_public_api, use_build_context_synchronously

import 'package:blog_app_val/models/post.dart';
import 'package:flutter/material.dart';

import '../constant.dart';
import '../models/api_response.dart';
import '../services/user_services.dart';
import '../services/post_services.dart';
import 'comment_screen.dart';
import 'login.dart';
import 'post_form.dart';

class PostScreen extends StatefulWidget {
  const PostScreen({super.key});

  @override
  _PostScreenState createState() => _PostScreenState();
}

class _PostScreenState extends State<PostScreen> {
  List<dynamic> _postList = [];
  int userId = 0;
  bool _loading = true;

  // get all posts
  Future<void> retrievePosts() async {
    userId = await getUserId();
    ApiResponse response = await getPosts();

    if(response.error == null){
      setState(() {
        _postList = response.data as List<dynamic>;
        _loading = _loading ? !_loading : _loading;
      });
    }
    else if(response.error == unauthorized){
      logout().then((value) => {
        Navigator.of(context).pushAndRemoveUntil(MaterialPageRoute(builder: (context)=>const Login()), (route) => false)
      });
    }
    else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text('${response.error}'),
      ));
    }
  }


  void _handleDeletePost(int postId) async {
    ApiResponse response = await deletePost(postId);
    if (response.error == null){
      retrievePosts();
    }
    else if(response.error == unauthorized){
      logout().then((value) => {
        Navigator.of(context).pushAndRemoveUntil(MaterialPageRoute(builder: (context)=>const Login()), (route) => false)
      });
    }
    else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text('${response.error}')
      ));
    }
  }



  // post like dislik
  void _handlePostLikeDislike(int postId) async {
    ApiResponse response = await likeUnlikePost(postId);

    if(response.error == null){
      retrievePosts();
    }
    else if(response.error == unauthorized){
      logout().then((value) => {
        Navigator.of(context).pushAndRemoveUntil(MaterialPageRoute(builder: (context)=>const Login()), (route) => false)
      });
    }
    else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text('${response.error}')
      ));
    }
  }

  @override
  void initState() {
    retrievePosts();
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return _loading ? const Center(child:CircularProgressIndicator()) :
    RefreshIndicator(
      onRefresh: () {
        return retrievePosts();
      },
      child: ListView.builder(
          itemCount: _postList.length,
          itemBuilder: (BuildContext context, int index){
            Post post = _postList[index];
            return Container(
              padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 10),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 6),
                        child: Row(
                          children: [
                            Container(
                              width: 38,
                              height: 38,
                              decoration: BoxDecoration(
                                  image: post.user!.image != null ?
                                  DecorationImage(image: NetworkImage('${post.user!.image}')) : null,
                                  borderRadius: BorderRadius.circular(25),
                                  color: Colors.amber
                              ),
                            ),
                            const SizedBox(width: 10,),
                            Text(
                              '${post.user!.name}',
                              style: const TextStyle(
                                  fontWeight: FontWeight.w600,
                                  fontSize: 17
                              ),
                            )
                          ],
                        ),
                      ),
                      post.user!.id == userId ?
                      PopupMenuButton(
                        child: const Padding(
                            padding: EdgeInsets.only(right:10),
                            child: Icon(Icons.more_vert, color: Colors.black,)
                        ),
                        itemBuilder: (context) => [
                          const PopupMenuItem(
                              value: 'edit',
                              child: Text('Edit')
                          ),
                          const PopupMenuItem(
                              value: 'delete',
                              child: Text('Delete')
                          )
                        ],
                        onSelected: (val){
                          if(val == 'edit'){
                            Navigator.of(context).push(MaterialPageRoute(builder: (context)=>PostForm(
                              title: 'Edit Post',
                              post: post,
                            )));
                          } else {
                            _handleDeletePost(post.id ?? 0);
                          }
                        },
                      ) : const SizedBox()
                    ],
                  ),
                  const SizedBox(height: 12,),
                  Text('${post.body}'),
                  post.image != null ?
                  Container(
                    width: MediaQuery.of(context).size.width,
                    height: 180,
                    margin: const EdgeInsets.only(top: 5),
                    decoration: BoxDecoration(
                        image: DecorationImage(
                            image: NetworkImage('${post.image}'),
                            fit: BoxFit.cover
                        )
                    ),
                  ) : SizedBox(height: post.image != null ? 0 : 10,),
                  Row(
                    children: [
                      kLikeAndComment(
                          post.likesCount ?? 0,
                          post.selfLiked == true ? Icons.favorite : Icons.favorite_outline,
                          post.selfLiked == true ? Colors.red : Colors.black54,
                              (){
                            _handlePostLikeDislike(post.id ?? 0);
                          }
                      ),
                      Container(
                        height: 25,
                        width: 0.5,
                        color: Colors.black38,
                      ),
                      kLikeAndComment(
                          post.commentsCount ?? 0,
                          Icons.sms_outlined,
                          Colors.black54,
                              (){
                            Navigator.of(context).push(MaterialPageRoute(builder: (context)=>CommentScreen(
                              postId: post.id,
                            )));
                          }
                      ),
                    ],
                  ),
                  Container(
                    width: MediaQuery.of(context).size.width,
                    height: 0.5,
                    color: Colors.black26,
                  ),
                ],
              ),
            );
          }
      ),
    );
  }
}