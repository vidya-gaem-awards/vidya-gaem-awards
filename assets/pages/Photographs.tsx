import React, {FormEvent, useState} from "react";
import Photograph from "../components/Photograph";
import {Advertisement} from "../entities";
import Alert from "react-bootstrap/Alert";
import Modal from "react-bootstrap/Modal";

interface PhotographsProps {
  ajaxUrl: string,
  decorations: Advertisement[]
}

interface PhotographsState {
  decorations: Advertisement[],
  dialogAction: 'new'|'edit',
  dialogError: null|string,
  dialogFileFilename: null|string,
  dialogFileObject: null|any,
  dialogFileUrl: null|string,
  dialogId: null|number,
  dialogName: string,
  dialogSpecial: boolean,
  dialogSubmitting: false|string,
  showDialog: boolean
}

class Photographs extends React.Component<PhotographsProps, PhotographsState> {

  constructor(props) {
    super(props)

    this.state = {
      decorations: props.decorations,

      dialogAction: 'new',
      dialogError: null,
      dialogFileFilename: null,
      dialogFileObject: null,
      dialogFileUrl: null,
      dialogId: undefined,
      dialogName: '',
      dialogSpecial: false,
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

  handleInputChange(event: React.ChangeEvent<HTMLInputElement>) {
    const target = event.target;
    const value = target.type === 'checkbox' ? target.checked : target.value;
    const name = target.name;

    this.setState<never>({
      [name]: value
    });
  }

  openEditDialog (decoration: Advertisement) {
    this.setState({
      dialogAction: 'edit',
      dialogFileFilename: 'Change file',
      dialogFileObject: null,
      dialogFileUrl: decoration.image?.url,
      dialogId: decoration.id,
      dialogName: decoration.name,
      dialogSpecial: decoration.special,
      showDialog: true
    })
  }

  openNewPhotoDialog () {
    this.setState({
      dialogAction: 'new',
      dialogFileFilename: null,
      dialogFileObject: null,
      dialogFileUrl: null,
      dialogId: undefined,
      dialogName: '',
      dialogSpecial: false,
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
    } else if (!confirm("Permanently delete this photograph?")) {
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

  render() {
    const deleteThis = require('../../public/img/delete-this.png').default;

    return (
      <div className="col">
        <h1 className="page-header board-header">Voting Page Decorations</h1>

        <div className="text-center">
          <button className="btn btn-sm btn-primary" id="new-award" type="button" data-bs-toggle="modal"
                  data-bs-target="#dialog-edit" onClick={this.openNewPhotoDialog.bind(this)}>
            <i className="fal fa-fw fa-plus"/> Add a new decoration
          </button>
        </div>

        <div className="decoration-container">
          {this.state.decorations.map((decoration) => (
            <Photograph name={decoration.name} image={decoration.image?.url} key={decoration.id} onClick={() => this.openEditDialog(decoration)} />
          ))}
        </div>

        <Modal id="dialog-edit" show={this.state.showDialog} onHide={this.handleClose.bind(this)}>
          <form className="form-horizontal" id="dialog-edit-form" encType="multipart/form-data" onSubmit={this.submitForm.bind(this)}>
            <Modal.Header closeButton>
              { this.state.dialogAction === 'new' ? 'Add a new decoration' : 'Edit decoration' }
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
                  <input type="hidden" id="info-action" name="action" value={this.state.dialogAction} />
                  <input type="hidden" id="info-id" name="id" value={this.state.dialogId} />

                  <div className="form-group row">
                    <label className="col-sm-3 col-form-label" htmlFor="info-image">Image</label>
                    <div className="col-sm-9">
                      <input type="file" id="info-image" name="image" className="form-control" onChange={this.handleFileChange}/>
                      <small className="form-text d-block">Recommended image dimensions: <strong>500 x 500</strong></small>
                      <small className="form-text d-block">Supported file types: <strong>.png, .jpg, .gif</strong></small>
                    </div>
                  </div>

                  <div className="form-group row">
                    <label className="col-sm-3 col-form-label" htmlFor="info-name">Name</label>
                    <div className="col-sm-9">
                      <input className="form-control" type="text" id="info-name"
                             placeholder="The Power of Shitposting" required
                             name="dialogName" maxLength={50} value={this.state.dialogName}
                             onChange={this.handleInputChange} />
                    </div>
                  </div>

                  <div className="form-group row">
                    <div className="offset-sm-3 col-sm-9">
                      <div className="form-check">
                        <input className="form-check-input" type="checkbox" id="info-special" name="dialogSpecial" checked={this.state.dialogSpecial} onChange={this.handleInputChange} />
                        <label className="form-check-label" htmlFor="info-special">Special</label>
                        <small className="form-text d-block">Special decorations won't show up in the normal rotation.</small>
                      </div>
                    </div>
                  </div>

                  <div className="mt-auto mb-2 text-end fw-bold" style={{fontSize: '18px'}}>Preview</div>
                </div>

                <Photograph name={this.state.dialogName} image={this.state.dialogFileUrl} />
              </div>
            </Modal.Body>

            <Modal.Footer>
              {
                this.state.dialogAction === 'edit' ?
                  <button className="btn btn-danger me-auto" id="dialog-edit-delete" type="button" onClick={this.submitDelete.bind(this)} disabled={Boolean(this.state.dialogSubmitting)}>
                    <img src={deleteThis} className="delete-this" alt="A picture of Counter pointing a gun at you, the viewer" /> Delete this
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

export default Photographs;
