import {CaptchaGame as CaptchaGameType} from "../entities";
import React, {FormEvent} from "react";
import Photograph from "../components/Photograph";
import Modal from "react-bootstrap/Modal";
import Alert from "react-bootstrap/Alert";
import CaptchaGame from "../components/CaptchaGame";
import '../styles/captcha-manager.scss';

interface CaptchaManagerProps {
  ajaxUrl: string,
  bulkImageUploaderUrl: string,
  games: CaptchaGameType[],
  rows: string[],
  columns: string[],
}

interface CaptchaManagerState {
  games: CaptchaGameType[],
  rows: string[],
  columns: string[],
  dialogAction: 'new'|'edit',
  dialogError: null|string,
  dialogFileFilename: null|string,
  dialogFileObject: null|any,
  dialogFileUrl: null|string,
  dialogId: null|number,
  dialogTitle: string,
  dialogFirst: string,
  dialogSecond: string,
  dialogSubmitting: false|string,
  showDialog: boolean
}

class CaptchaManager extends React.Component<CaptchaManagerProps, CaptchaManagerState> {

  constructor(props) {
    super(props)

    this.state = {
      games: props.games,
      rows: props.rows,
      columns: props.columns,

      dialogAction: 'new',
      dialogError: null,
      dialogFileFilename: null,
      dialogFileObject: null,
      dialogFileUrl: null,
      dialogId: undefined,
      dialogTitle: '',
      dialogFirst: '',
      dialogSecond: '',
      dialogSubmitting: false,
      showDialog: false
    }

    this.handleFileChange = this.handleFileChange.bind(this);
    this.handleInputChange = this.handleInputChange.bind(this);
    this.openEditDialog = this.openEditDialog.bind(this);
  }

  handleClose () {
    this.setState({ showDialog: false} )
  }

  handleFileChange(event: React.ChangeEvent<HTMLInputElement>) {
    const files = event.target.files;
    if (files.length === 0) {

      this.setState({
        dialogFileFilename: null,
        dialogFileObject: null,
        dialogFileUrl: null
      })
      return
    }

    const file = files[0];
    this.setState({
      dialogFileFilename: file.name,
      dialogFileObject: file,
      dialogFileUrl: URL.createObjectURL(file)
    })
  }

  isSelect(input: HTMLInputElement|HTMLSelectElement): input is HTMLSelectElement {
    return (input as HTMLSelectElement).options !== undefined;
  }

  handleInputChange(event: React.ChangeEvent<HTMLInputElement|HTMLSelectElement>) {
    const target = event.target;
    const value = (!this.isSelect(target) && target.type === 'checkbox') ? target.checked : target.value;
    const name = target.name;

    this.setState<never>({
      [name]: value
    });
  }

  openEditDialog (game: CaptchaGameType) {
    this.setState({
      dialogAction: 'edit',
      dialogFileFilename: 'Change file',
      dialogFileObject: null,
      dialogFileUrl: game.image?.url,
      dialogId: game.id,
      dialogTitle: game.title,
      dialogFirst: game.first,
      dialogSecond: game.second,
      showDialog: true
    })
  }

  openNewDialog () {
    this.setState({
      dialogAction: 'new',
      dialogFileFilename: null,
      dialogFileObject: null,
      dialogFileUrl: null,
      dialogId: undefined,
      dialogTitle: '',
      dialogFirst: null,
      dialogSecond: null,
      showDialog: true
    })
  }

  submitForm (event: FormEvent) {
    event.preventDefault();

    if (this.state.dialogSubmitting) {
      return;
    }
    this.setState({dialogSubmitting: 'saving'});

    // Send through the AJAX request
    const formData = new FormData(event.target as HTMLFormElement);

    $.ajax({
      url: this.props.ajaxUrl,
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false
    }).done(response => {
      if (response.success) {
        window.location.reload();
      } else {
        this.setState({
          dialogError: response.error,
          dialogSubmitting: false
        });
      }
    });
  }

  submitDelete () {
    if (this.state.dialogSubmitting) {
      return;
    } else if (!confirm("Permanently delete this game?")) {
      return;
    }

    this.setState({dialogSubmitting: 'deleting'});

    const data = [
      {name: "action", value: "delete"},
      {name: "id", value: this.state.dialogId}
    ];

    $.post(this.props.ajaxUrl, data, response => {
      if (response.success) {
        window.location.reload();
      } else {
        this.setState({
          dialogError: response.error,
          dialogSubmitting: false
        });
      }
    }, "json");
  }

  ucfirst(string: string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  gameByRowAndColumn(row: string, column: string) {
    return this.state.games.filter(game => game.first === row && game.second === column)[0] ?? null;
  }

  render() {
    const deleteThis = require('../../public/img/delete-this.png').default;

    return (
      <div className="col">
        <h1 className="page-header board-header">Voting Page Captcha Games</h1>

        <div className="text-center">
          <button className="btn btn-sm btn-primary" id="new-award" type="button" data-bs-toggle="modal"
                  data-bs-target="#dialog-edit" onClick={this.openNewDialog.bind(this)}>
            <i className="fal fa-fw fa-plus"/> Add a new game
          </button>

          <a href={this.props.bulkImageUploaderUrl} className="btn btn-sm btn-secondary ms-2">
            <i className="fal fa-fw fa-upload"/> Bulk image uploader
          </a>
        </div>

        <div className="table-responsive table-responsive-xxl mt-4">
          <table className="captcha-table table">
            <thead>
            <tr>
              <th></th>
              {this.state.columns.map((column) => (
                <th key={column}>{this.ucfirst(column)}</th>
              ))}
            </tr>
            </thead>
            <tbody>
            {this.state.rows.map((row) => (
              <tr key={row}>
                <th>{this.ucfirst(row)}</th>
                {this.state.columns.map((column) => (
                  <td key={`${row}-${column}`}>
                    {this.gameByRowAndColumn(row, column) ? <CaptchaGame game={this.gameByRowAndColumn(row, column)} onClick={() => this.openEditDialog(this.gameByRowAndColumn(row, column))} /> : ''}
                  </td>
                ))}
              </tr>
            ))}
            </tbody>
          </table>
        </div>

        <Modal id="dialog-edit" show={this.state.showDialog} onHide={this.handleClose.bind(this)}>
          <form className="form-horizontal" id="dialog-edit-form" encType="multipart/form-data" onSubmit={this.submitForm.bind(this)}>
            <Modal.Header closeButton>
              { this.state.dialogAction === 'new' ? 'Add a new game' : 'Edit game' }
            </Modal.Header>

            <Modal.Body>
              {
                this.state.dialogError ?
                  <Alert variant="danger" dismissible onClose={() => {this.setState({dialogError: null})}}>
                    <strong>Error:</strong> {this.state.dialogError}
                  </Alert>
                  : ''
              }
              <div className="d-flex">
                <div className="flex-grow-1 me-4 d-flex flex-column">
                  <input type="hidden" id="info-action" name="action" value={this.state.dialogAction}/>
                  <input type="hidden" id="info-id" name="id" value={this.state.dialogId}/>

                  <div className="form-group row">
                    <label className="col-sm-3 col-form-label" htmlFor="info-image">Image</label>
                    <div className="col-sm-9">
                      <input type="file" id="info-image" name="image" className="form-control"
                             onChange={this.handleFileChange}/>
                      <small className="form-text d-block">Required image dimensions: <strong>128 x
                        128</strong></small>
                      <small className="form-text d-block">Supported file types: <strong>.png</strong></small>
                    </div>
                  </div>

                  <div className="form-group row">
                    <label className="col-sm-3 col-form-label" htmlFor="info-title">Title</label>
                    <div className="col-sm-9">
                      <input className="form-control" type="text" id="info-title"
                             placeholder="Half-Life 2" required
                             name="dialogTitle" maxLength={50} value={this.state.dialogTitle}
                             onChange={this.handleInputChange}/>
                    </div>
                  </div>

                  <div className="form-group row">
                    <label className="col-sm-3 col-form-label" htmlFor="info-first">Row</label>
                    <div className="col-sm-9">
                      <select className="form-select" id="info-first"
                              required
                              name="dialogFirst"
                              value={this.state.dialogFirst}
                              onChange={this.handleInputChange}>
                        <option value=""></option>
                        {this.state.rows.map((value) =>
                          <option value={value} key={value}>{value}</option>
                        )}
                      </select>
                    </div>
                  </div>

                  <div className="form-group row">
                    <label className="col-sm-3 col-form-label" htmlFor="info-second">Column</label>
                    <div className="col-sm-9">
                      <select className="form-select" id="info-second"
                              required
                              name="dialogSecond"
                              value={this.state.dialogSecond}
                              onChange={this.handleInputChange}>
                        <option value=""></option>
                        {this.state.columns.map((value) =>
                          <option value={value} key={value}>{value}</option>
                        )}
                      </select>
                    </div>
                  </div>
                </div>

                <div className="captcha-game-preview">
                  <div className="mt-auto mb-2 text-center fw-bold" style={{fontSize: '18px'}}>Preview</div>
                  <CaptchaGame game={{title: this.state.dialogTitle}} url={this.state.dialogFileUrl} />
                </div>
              </div>
            </Modal.Body>

            <Modal.Footer>
              {
                this.state.dialogAction === 'edit' ?
                  <button className="btn btn-danger me-auto" id="dialog-edit-delete" type="button"
                          onClick={this.submitDelete.bind(this)} disabled={Boolean(this.state.dialogSubmitting)}>
                    <img src={deleteThis} className="delete-this"
                         alt="A picture of Counter pointing a gun at you, the viewer" /> Delete this
                  </button>
                  : ''
              }
              {
                this.state.dialogSubmitting ?
                  <>
                    <i className="far fa-circle-notch fa-spin me-1" /> {this.state.dialogSubmitting}...&nbsp;
                  </>
                  : ''
              }
              <button className="btn btn-outline-dark" type="button" onClick={this.handleClose.bind(this)} disabled={Boolean(this.state.dialogSubmitting)}>Cancel</button>
              <button className="btn btn-primary" id="dialog-edit-submit" type="submit" disabled={Boolean(this.state.dialogSubmitting)}>Submit</button>
            </Modal.Footer>
          </form>
        </Modal>
      </div>
    )
  }
}

export default CaptchaManager;
