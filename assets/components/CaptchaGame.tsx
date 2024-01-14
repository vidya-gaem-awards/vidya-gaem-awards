import React from 'react';
import {CaptchaGame as CaptchaGameType} from "../entities";
import '../styles/captchas.scss';

export interface CaptchaGameProps {
  game: Partial<CaptchaGameType>;
  url?: string;
  onClick?(): any;
  selected?: boolean;
  showTitle?: boolean;
  showAttributes?: boolean;
}

class CaptchaGame extends React.Component<CaptchaGameProps> {
  render() {
    const url = this.props.url || this.props.game?.image?.url;
    return (
      <div className={`captcha-game ${this.props.selected ? 'selected' : ''}`} onClick={this.props.onClick}>
        <div className="captcha-game-inner" style={{ backgroundImage: url ? `url("${url}")` : null}}>
          {this.props.showAttributes &&
            <div style={{backgroundColor: 'black'}}>
              {this.props.game.first} {this.props.game.second}
            </div>
          }
          {(!url || this.props.showTitle) &&
              <div style={{backgroundColor: 'black'}}>
                {this.props.game.title}
              </div>
          }
        </div>
        <div className="rc-imageselect-checkbox" />
      </div>
    )
  }
}

export default CaptchaGame;
