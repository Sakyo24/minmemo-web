import 'package:flutter_dotenv/flutter_dotenv.dart';

class AdMob {
  static bool isTest = (dotenv.get('AD_TEST_FLAG') == 'true') ? true : false;

  static Map<String, Map<String, String>> productionAdIds = {
    'ios': {
      'banner': 'ca-app-pub-9050779314039155/5275884586',
      'interstitial': 'ca-app-pub-9050779314039155/7123356710',
    },
    'android': {
      'banner': 'ca-app-pub-9050779314039155/6644806577',
      'interstitial': 'ca-app-pub-9050779314039155/6588966252',
    }
  };

  static Map<String, Map<String, String>> testAdIds = {
    'ios': {
      'banner': 'ca-app-pub-3940256099942544/2934735716',
      'interstitial': 'ca-app-pub-3940256099942544/5135589807'
    },
    'android': {
      'banner': 'ca-app-pub-3940256099942544/6300978111',
      'interstitial': 'ca-app-pub-3940256099942544/8691691433'
    }
  };

  static String getAdId({required String deviceType, required String adType}) {
    if (isTest == true) {
      return testAdIds[deviceType]![adType].toString();
    } else {
      return productionAdIds[deviceType]![adType].toString();
    }
  }
}
