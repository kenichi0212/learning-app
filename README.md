# 学習管理 App

AIによる離席検知を備えた、学習セッション管理・可視化アプリです。
学習中の「実質的な時間」を記録し、目標達成を支援します。

URL: （デプロイ先URLをここに記載）

## 🛠 使用技術

### Backend

- Framework: Laravel 12
- Authentication: Laravel Breeze
- Language: PHP 8.2
- Database: PostgreSQL（Laravel Eloquent）
- Testing: PHPUnit

### Frontend

- Framework: Alpine.js
- Styling: Tailwind CSS / PostCSS
- Build Tool: Vite
- AI Library: TensorFlow.js（BlazeFace）
- API: Screen Capture API / MediaDevices API（getDisplayMedia / getUserMedia）

## 📋 機能、非機能一覧

### 機能要件

- ユーザー管理: ユーザー登録・ログイン・ログアウト、プロフィール編集
- 目標設定: 学習タイトル、期限、If-Then ルールの作成・管理
- セッション制御: 学習の開始・一時停止・終了、リアルタイムタイマー表示
- 自動ロギング: 離席検知・画面変化の定期記録
- データ可視化: 学習履歴の一覧表示とダッシュボード集計

### 非機能要件

- プライバシー: 顔検知はブラウザ内で完結し、画像はサーバーへ送信しない
- パフォーマンス: ページネーションとクエリ最適化で大量ログでも快適に動作
- バリデーションと認可: 入力検証と、認証ユーザーのみのデータアクセスを担保
