import React from "react";
import {CaptchaGame as CaptchaGameType} from "../entities";
import ReactDOM from "react-dom";
import CaptchaGame from "./CaptchaGame";
import Modal from "react-bootstrap/Modal";

export interface CaptchaProps {
  games: CaptchaGameType[];
  rows: string[],
  columns: string[],
  userSettings: CaptchaUserSettings;
  onCompletion?: (score: number|null) => void;
}

export interface CaptchaUserSettings {
  showTitles: boolean;
  maxThreeFailures: boolean;
  neverShowAgain: boolean;
  allowPartialPass: boolean;
  completions: number;
}

interface CaptchaState {
  visible: boolean;
  games: CaptchaGameType[];
  correctGames: CaptchaGameType[];
  row: string;
  column: string;
  initialized: boolean;
  selected: number[];
  error?: string;
  waiting: boolean;
  failures: number;
  showHelp: boolean;
  userSettings: CaptchaUserSettings;
  bonus: boolean;
}

function randChoice<T>(arr: Array<T>): T {
  return arr[Math.floor(Math.random() * arr.length)]
}

function shuffleArray<T>(array: T[]): T[] {
  let m = array.length;
  let i: number;
  while (m) {
    i = (Math.random() * m--) >>> 0;
    [array[m], array[i]] = [array[i], array[m]]
  }
  return array;
}

class Captcha extends React.Component<CaptchaProps, CaptchaState> {

  constructor(props: CaptchaProps) {
    super(props);

    this.state = {
      visible: false,
      games: [],
      correctGames: [],
      row: '',
      column: '',
      initialized: false,
      selected: [],
      waiting: false,
      failures: 0,
      showHelp: false,
      userSettings: props.userSettings,
      bonus: false,
    };

    this.refresh = this.refresh.bind(this);
    this.show = this.show.bind(this);
    this.hide = this.hide.bind(this);
    this.checkResult = this.checkResult.bind(this);
    this.updateUserSetting = this.updateUserSetting.bind(this);
    this.neverShowAgain = this.neverShowAgain.bind(this);
  }

  componentDidMount() {
    this.refresh();
  }

  show(bonus: boolean = false) {
    this.setState({
      visible: true,
      waiting: false,
      error: undefined,
      bonus
    });
    this.refresh();
  }

  hide() {
    this.setState({ visible: false });
  }

  refresh() {
    const row = randChoice(this.props.rows);
    const column = randChoice(this.props.columns);
    const correctGames = [];

    correctGames.push(this.selectGameOfType(row, column));
    correctGames.push(this.selectGameOfType(row, undefined, false, correctGames.map(game => game.id)));
    correctGames.push(this.selectGameOfType(undefined, column, false, correctGames.map(game => game.id)));

    let games = [...correctGames];

    for (let i = 0; i < 6; i++) {
      const game = this.selectGameOfType(row, column, true, games.map(game => game.id));
      if (game) {
        games.push(game);
      }
    }

    games = shuffleArray(games);

    this.setState({ games, correctGames, row, column, selected: [], initialized: true });
  }

  checkResult() {
    let score = 0;

    if (this.state.selected.length !== 3) {
      this.setState({ error: 'Please select exactly three games.' });
      return;
    }

    for (const game of this.state.correctGames) {
      if (this.state.selected.includes(game.id)) {
        score++;
      }
    }

    $.post('/captcha/result', {
      row: this.state.row,
      column: this.state.column,
      games: this.state.games.map(game => game.id),
      selected: this.state.selected,
      score,
    });

    this.setState({ waiting: true });

    const messages = {
      0: 'You got 0 out of 3 correct.',
      1: 'You got 1 out of 3 correct.',
      2: 'You got 2 out of 3 correct.',
      3: 'Perfect score!',
    };

    let feedback = messages[score] || 'You got ' + score + ' out of 3 correct.';

    if (!this.state.bonus) {
      feedback += ' Your votes have been submitted.';
    }

    this.setState({
      error: feedback
    });

    setTimeout(() => {
      this.hide();
      if (this.props.onCompletion) {
        this.props.onCompletion(score);
      }
    }, this.state.bonus ? 2000 : 3000);
  }

  updateUserSetting(setting: keyof CaptchaUserSettings, value: boolean) {
    const userSettings: CaptchaUserSettings = {
      ...this.state.userSettings,
      [setting]: value
    };

    this.setState({ userSettings });
    window.localStorage.setItem(`captcha-${setting}`, value ? 'true' : 'false');
  }

  selectGameOfType(row?: string, column?: string, invert?: boolean, exclude: number[] = []): CaptchaGameType|null {
    let games: CaptchaGameType[];

    if (row && column) {
      games = this.props.games.filter(game => {
        if (invert) {
          return game.first !== row && game.second !== column;
        }
        return game.first === row && game.second === column;
      });
    } else if (row) {
      games = this.props.games.filter(game => {
        if (invert) {
          return game.first !== row;
        }
        return game.first === row;
      });
    } else if (column) {
      games = this.props.games.filter(game => {
        if (invert) {
          return game.second !== column;
        }
        return game.second === column;
      });
    } else {
      games = this.props.games;
    }

    games = games.filter(game => !exclude.includes(game.id));

    if (games.length > 0) {
      return randChoice(games);
    }

    return null;
  }

  onGameClick(game: CaptchaGameType) {
    if (this.state.waiting) {
      return;
    }
    const selected = this.state.selected;
    if (selected.includes(game.id)) {
      selected.splice(selected.indexOf(game.id), 1);
    } else {
      selected.push(game.id);
    }
    this.setState({selected});

    if (selected.length >= 3) {
      this.setState({error: undefined});
    }
  }

  neverShowAgain() {
    this.setState({
      error: 'This CAPTCHA will never be shown again. Your votes have been saved.',
      waiting: true,
    });
    this.updateUserSetting('neverShowAgain', true);

    setTimeout(() => {
      this.hide();
      if (this.props.onCompletion) {
        this.props.onCompletion(null);
      }
    }, 3000);
  }

  render() {
    if (!this.state.initialized) {
      return null;
    }

    return (
      <Modal show={this.state.visible} onHide={this.hide} dialogClassName="captcha-modal" fullscreen="sm-down" scrollable={true}>
        <Modal.Body>
          <div className="captcha-box"> {/* rc-imageselect */}
            <div className="rc-imageselect-payload">
              <div className="rc-imageselect-instructions">
                <div className="rc-imageselect-desc-wrapper">
                  <div className="rc-imageselect-desc-no-canonical">
                    Select all games that are
                    <div className="captcha-box-focus">
                      <strong>{this.state.row}</strong> or <strong>{this.state.column}</strong>
                    </div>
                  </div>
                </div>
                <button type="button" className="btn-close btn-close-white" aria-label="Close" onClick={this.hide}></button>
              </div>
              {
                this.state.showHelp
                  ?
                  <div className={"captcha-help"}>
                    <strong>Hints:</strong>
                    <ul>
                      <li>There are always exactly three correct answers.</li>
                      <li>One game will have both attributes, one game will have just the first attribute, and one game
                        will
                        have just the second attribute.
                      </li>
                      <li>You can refresh as many times as you like.</li>
                    </ul>

                    <strong>Cheats:</strong>
                    <div className="my-2">
                      <button
                        className="rc-button-default"
                        disabled={this.state.userSettings.showTitles}
                        onClick={() => this.updateUserSetting('showTitles', true)}>
                        Show game titles
                        {this.state.userSettings.showTitles &&
                            <i className="fas fa-check ms-2"></i>
                        }
                      </button>
                    </div>
                    <div className="my-2">
                      <button
                        className="rc-button-default"
                        disabled={this.state.userSettings.allowPartialPass}
                        onClick={() => this.updateUserSetting('allowPartialPass', true)}>
                        Allow 2 out of 3 to pass
                        {this.state.userSettings.allowPartialPass &&
                            <i className="fas fa-check ms-2"></i>
                        }
                      </button>
                    </div>
                    <div className="my-2">
                      <button className="rc-button-default">
                        Skip CAPTCHA just this time
                      </button>
                    </div>
                    <div className="my-2">
                      <button
                        className="rc-button-default"
                        disabled={this.state.userSettings.maxThreeFailures}
                        onClick={() => this.updateUserSetting('maxThreeFailures', true)}>
                        Skip all CAPTCHAs after 3 failures
                        {this.state.userSettings.maxThreeFailures &&
                            <i className="fas fa-check ms-2"></i>
                        }
                      </button>
                    </div>
                    <div className="my-2">
                      <button
                        className="rc-button-default surrender"
                        disabled={this.state.userSettings.neverShowAgain}
                        onClick={() => this.updateUserSetting('neverShowAgain', true)}>
                        Never show CAPTCHAs again
                        {this.state.userSettings.neverShowAgain &&
                            <i className="fas fa-check ms-2"></i>
                        }
                      </button>
                    </div>
                  </div>
                  :
                  <>
                    <div className={`captcha-box-items ${this.state.waiting ? 'waiting' : ''}`}>
                      {this.state.games.map((game) => (
                        <CaptchaGame
                          game={game}
                          key={game.id}
                          selected={this.state.selected.includes(game.id)}
                          onClick={() => this.onGameClick(game)}
                          showTitle={this.state.userSettings.showTitles}
                        />
                      ))}
                    </div>
                    {
                      this.state.error &&
                        <div className="rc-imageselect-incorrect-response">
                          {this.state.error}
                        </div>
                    }
                  </>
              }
            </div>
            <div className="rc-footer">
              <div className="rc-separator"></div>
              <div className="rc-controls">
                <div className="rc-primary-controls">
                  {this.state.showHelp
                    ?
                    <div className="rc-verify-button-holder">
                      <button className="rc-button-default goog-inline-block"
                              onClick={() => this.setState({showHelp: false})}>
                        Go Back
                      </button>
                    </div>
                    :
                    <>
                      <div className="rc-buttons">
                        <div className="rc-button-holder">
                          <button className="rc-button goog-inline-block rc-button-reload" onClick={this.refresh}
                                  disabled={this.state.waiting}></button>
                        </div>
                      </div>
                      <div className="rc-verify-button-holder">
                        <button className="rc-button-default goog-inline-block" onClick={this.checkResult}
                                disabled={this.state.waiting}>
                          Verify
                        </button>
                      </div>
                      {this.state.userSettings.completions >= 1 && !this.state.bonus &&
                          <div className="rc-verify-button-holder">
                              <button className="rc-button-default surrender goog-inline-block"
                                      onClick={this.neverShowAgain}
                                      disabled={this.state.waiting}>
                                  Never Show Again
                              </button>
                          </div>
                      }
                    </>
                  }
                </div>
              </div>
            </div>
          </div>
        </Modal.Body>
      </Modal>
    )
  }
}

export const renderCaptcha = (props: CaptchaProps) => {
  ReactDOM.render(
    <Captcha {...props} ref={(element) => window.captchaReact = element}/>,
    document.getElementById('react-captcha')
  );
}

export default Captcha;
