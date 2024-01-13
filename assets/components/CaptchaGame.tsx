import React from 'react';
import {CaptchaGame as CaptchaGameType} from "../entities";
import '../styles/captchas.scss';

export interface CaptchaGameProps {
  game: Partial<CaptchaGameType>;
  url?: string;
  onClick?(): any;
}

class CaptchaGame extends React.Component<CaptchaGameProps> {
  render() {
    const url = this.props.url || this.props.game?.image?.url;
    return (
      <div className="captcha-game" onClick={this.props.onClick} style={{ backgroundImage: url ? `url("${url}")` : null}}>
        {!url && this.props.game.title}
      </div>
    )
  }
}

export default CaptchaGame;
