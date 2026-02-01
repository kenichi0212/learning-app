# 学習管理 App
<img width="1442" height="860" alt="learning01" src="https://github.com/user-attachments/assets/41deef62-865f-48d0-a6d4-304d952063c9" />
<img width="1411" height="442" alt="learnig02" src="https://github.com/user-attachments/assets/ead5f26a-7cd9-4277-b63b-6991d0373a37" />
<img width="1441" height="557" alt="learning03" src="https://github.com/user-attachments/assets/1e2e305f-8a3f-47af-8122-90b828cdd3a5" />


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
