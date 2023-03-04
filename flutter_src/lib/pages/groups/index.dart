import 'package:flutter/material.dart';
import 'package:http/http.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'dart:convert';
import 'dart:io';
import '../../model/admob.dart';
import '../../model/group.dart';
import '../user/show.dart';
import '../index.dart';
import '../../utils/network.dart';

class GroupsIndexPage extends StatefulWidget {
  const GroupsIndexPage({super.key});

  @override
  State<GroupsIndexPage> createState() => _GroupsIndexPageState();
}

class _GroupsIndexPageState extends State<GroupsIndexPage> {
  bool _isLoading = true;

  // グループリスト取得処理
  List items = [];
  Future<void> getGroups() async {
    Response? response;
    try {
      response = await Network().getData('/api/groups');
      var jsonResponse = jsonDecode(response.body);
      print(jsonResponse);
      setState(() {
        items = jsonResponse['groups'];
        _isLoading = false;
      });
    } catch (e) {
      debugPrint(e.toString());
      setState(() {
        _isLoading = false;
      });
    }
  }

  // TODO:広告の共通化
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
    getGroups();
    initAd();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('グループ一覧'),
        backgroundColor: const Color.fromARGB(255, 60, 0, 255),
        automaticallyImplyLeading: false
      ),
      body: _isLoading
      ? const Center(
        child: CircularProgressIndicator()
      )
      : ListView.builder(
        itemCount: items.length,
        itemBuilder: (context, index) {
          Map<String, dynamic> data = items[index] as Map<String, dynamic>;
          final Group fetchGroup = Group(
            id: data['id'],
            name: data['name'],
            owner_user_id: data['owner_user_id'],
            created_at: data['created_at'],
            updated_at: data['updated_at']
          );

          return ListTile(
            title: Text(fetchGroup.name),
            trailing: IconButton(
              onPressed: () {
                showModalBottomSheet(context: context, builder: (context) {
                  return SafeArea(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        ListTile(
                          onTap: () {
                            // TODO:グループ編集ページに遷移 
                          },
                          leading: const Icon(Icons.edit),
                          title: const Text('編集')
                        ),
                        ListTile(
                          onTap: () {
                            // TODO:グループ削除処理 
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
              // TODO:グループ詳細ページに遷移 
            }
          );
        }
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {},
        tooltip: 'グループ追加',
        child: const Icon(Icons.add)
      ),
      // TODO:ボトムメニュー共通化
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
            currentIndex: 1,
            items: const [
              BottomNavigationBarItem(
                label: 'メモ',
                icon: Icon(Icons.list)
              ),
              BottomNavigationBarItem(
                label: 'グループ',
                icon: Icon(Icons.groups)
              ),
              BottomNavigationBarItem(
                label: 'マイページ',
                icon: Icon(Icons.perm_identity)
              )
            ],
            onTap: (int value) {
              if (value == 0) {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const IndexPage()),
                );
              } else if (value == 2) {
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