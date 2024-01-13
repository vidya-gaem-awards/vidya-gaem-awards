import React from 'react'
import ReactDOM from 'react-dom'

import {CaptchaGame} from "./entities";
import CaptchaManager from "./pages/CaptchaManager";

declare var ajaxUrl: string;
declare var bulkImageUploaderUrl: string;
declare var games: Map<number, CaptchaGame>
declare var rows: string[];
declare var columns: string[];

ReactDOM.render(
  <CaptchaManager
    ajaxUrl={ajaxUrl}
    bulkImageUploaderUrl={bulkImageUploaderUrl}
    games={Object.values(games)}
    rows={rows}
    columns={columns}
  />,
  document.getElementById('react-captcha-manager')
);
