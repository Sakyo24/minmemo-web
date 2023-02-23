import 'package:flutter/material.dart';
import 'package:http/http.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'dart:convert';
import 'dart:io';
import '../model/admob.dart';
import '../model/todo.dart';
import './user/show.dart';
import './show.dart';
import './create_edit.dart';

class IndexPage extends StatefulWidget {
  const IndexPage({super.key});

  @override
  State<IndexPage> createState() => _IndexPageState();
}

class _IndexPageState extends State<IndexPage> {
  // Todoリスト取得処理
  List items = [];
  Future<void> getTodos() async {
    var url = Uri.parse(dotenv.get('APP_URL') + '/api/todos');
    Response response = await get(url);

    var jsonResponse = jsonDecode(response.body);
    setState(() {
      items = jsonResponse['todos'];
    });
  }

  // 削除処理
  Future<void> deleteTodo(id) async {
    var url = Uri.parse(dotenv.get('APP_URL') + '/api/todos/' + id.toString());
    await delete(url).then((response) {
      print(response.statusCode);
      setState(() {});
    });
  }

  // バナー広告
  late BannerAd bannerAd;
  bool isAdLoaded = false;
  void initAd() {
    this.bannerAd = BannerAd(
      adUnitId: Platform.isAndroid ? AdMob.getAdId(deviceType: 'android', adType: 'banner') : AdMob.getAdId(deviceType: 'ios', adType: 'banner'),
      size: AdSize.banner,
      request: AdRequest(),
      listener: BannerAdListener(
        onAdLoaded: (Ad ad) {
          setState(() {
            isAdLoaded = true;
          });
        }
      )
    )..load();
  }

  @override
  void initState() {
    super.initState();
    getTodos();
    initAd();
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Todo一覧'),
        backgroundColor: const Color.fromARGB(255, 60, 0, 255),
        automaticallyImplyLeading: false
      ),
      body: ListView.builder(
        itemCount: items.length,
        itemBuilder: (context, index) {
          Map<String, dynamic> data = items[index] as Map<String, dynamic>;
          final Todo fetchTodo = Todo(
            id: data['id'],
            title: data['title'],
            detail: data['detail'],
            created_at: data['created_at'],
            updated_at: data['updated_at']
          );

          return ListTile(
            title: Text(fetchTodo.title),
            trailing: IconButton(
              onPressed: () {
                showModalBottomSheet(context: context, builder: (context) {
                  return SafeArea(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        ListTile(
                          onTap: () {
                            Navigator.pop(context);
                            Navigator.push(context, MaterialPageRoute(builder: (context) => CreateEditPage(currentTodo: fetchTodo)));
                          },
                          leading: const Icon(Icons.edit),
                          title: const Text('編集')
                        ),
                        ListTile(
                          onTap: () async {
                            await deleteTodo(fetchTodo.id);
                            await getTodos();
                            Navigator.pop(context);
                          },
                          leading: const Icon(Icons.delete),
                          title: const Text('削除')
                        )
                      ]
                    )
                  );
                });
              },
              icon: const Icon(Icons.edit)
            ),
            onTap: () {
              Navigator.push(context, MaterialPageRoute(builder: (context) => ShowPage(fetchTodo)));
            }
          );
        }
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.push(context, MaterialPageRoute(builder: (context) => const CreateEditPage()));
        },
        tooltip: 'Todo追加',
        child: const Icon(Icons.add)
      ),
      bottomNavigationBar: Column(
        mainAxisAlignment: MainAxisAlignment.end,
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: isAdLoaded ? bannerAd.size.width.toDouble() : 0,
            height: isAdLoaded ? bannerAd.size.height.toDouble() : 0,
            child: isAdLoaded ? AdWidget(ad: bannerAd) : Container()
          ),
          BottomNavigationBar(
            currentIndex: 0,
            items: const [
              BottomNavigationBarItem(
                label: 'メモ',
                icon: Icon(Icons.list)
              ),
              BottomNavigationBarItem(
                label: 'マイページ',
                icon: Icon(Icons.perm_identity)
              )
            ],
            onTap: (int value) {
              if (value == 1) {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const UserShowPage()),
                );
              }
            }
          )
        ]
      )
    );
  }
}
