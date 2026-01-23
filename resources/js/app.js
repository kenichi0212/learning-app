import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// API関数をグローバルに公開
import { apiStartSession, apiUpdateSession, apiStopSession } from './api.js';
window.apiStartSession = apiStartSession;
window.apiUpdateSession = apiUpdateSession;
window.apiStopSession = apiStopSession;

// 監視機能をグローバルに公開
import { loadModel, checkScreenDifference, checkFaceDetection, resetScreenCanvas, startMonitoringInterval, stopMonitoringInterval } from './monitor.js';
window.loadModel = loadModel;
window.checkScreenDifference = checkScreenDifference;
window.checkFaceDetection = checkFaceDetection;
window.resetScreenCanvas = resetScreenCanvas;
window.startMonitoringInterval = startMonitoringInterval;
window.stopMonitoringInterval = stopMonitoringInterval;

// タイマー機能をグローバルに公開
import { updateTimerText, startTimerUI, stopTimer, getSeconds, getLastUpdateSeconds, setLastUpdateSeconds, resetTimer, getTimerState } from './timer.js';
window.updateTimerText = updateTimerText;
window.startTimerUI = startTimerUI;
window.stopTimer = stopTimer;
window.getSeconds = getSeconds;
window.getLastUpdateSeconds = getLastUpdateSeconds;
window.setLastUpdateSeconds = setLastUpdateSeconds;
window.resetTimer = resetTimer;
window.getTimerState = getTimerState;

// UI制御機能をグローバルに公開
import { showRunningButtons, showPausedButtons, setStartButtonLoading, resetStartButton, showCameraOn, showPipReconnect, hidePipReconnect } from './ui-controller.js';
window.showRunningButtons = showRunningButtons;
window.showPausedButtons = showPausedButtons;
window.setStartButtonLoading = setStartButtonLoading;
window.resetStartButton = resetStartButton;
window.showCameraOn = showCameraOn;
window.showPipReconnect = showPipReconnect;
window.hidePipReconnect = hidePipReconnect;

// セッション管理機能をグローバルに公開
import { startSystem } from './session-manager.js';
window.startSystem = startSystem;

// セッション制御機能をグローバルに公開
import { setupSessionHandlers } from './session-controller.js';
window.setupSessionHandlers = setupSessionHandlers;
