import 'package:flutter/material.dart';
import 'package:http/http.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'dart:convert';
import 'dart:io';
import '../../model/admob.dart';
import '../../utils/network.dart';
import '../top.dart';
import '../index.dart';

class UserShowPage extends StatefulWidget {
  const UserShowPage({super.key});

  @override
  State<UserShowPage> createState() => _UserShowPageState();
}

class _UserShowPageState extends State<UserShowPage> {
  bool _isLoading = false;

  // ログアウト処理
  Future<void> _logout() async {
    setState(() {
      _isLoading = true;
    });

    Response? res;
    try {
      res = await Network().getData('/api/logout');
    } catch (e) {
      debugPrint(e.toString());
    }

    if (res == null) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("エラーが発生しました。"))
        );
      }
      setState(() {
        _isLoading = false;
      });
      return;
    }

    var body = json.decode(res.body);
    // エラーの場合
    if (res.statusCode != 200) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(body['message']))
        );
      }
      setState(() {
        _isLoading = false;
      });
      return;
    }

    // 正常の場合
    SharedPreferences localStorage = await SharedPreferences.getInstance();
    localStorage.remove('user');
    localStorage.remove('token');

    if (!mounted) return;
    Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => const TopPage())
    );
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
    initAd();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("マイページ"),
        automaticallyImplyLeading: false
      ),
      body: _isLoading
      ? const Center(
        child: CircularProgressIndicator()
      )
      : Center(
        child: Column(
          children: [
            ElevatedButton(
              onPressed: () {
                _logout();
              },
              child: const Text("ログアウト")
            )
          ]
        ),
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
            currentIndex: 1,
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
              if (value == 0) {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const IndexPage()),
                );
              }
            }
          )
        ]
      )
    );
  }
}