import React from 'react';

export interface PhotographProps {
    id: null | number;
    name: string;
    image: string;
}

class Photograph extends React.Component<PhotographProps> {
    render() {
        return (
            <div className="photograph" data-toggle="modal" data-target="#dialog-edit" data-id={this.props.id}>
                <div className="photo-image" style={{ backgroundImage: `url("${this.props.image}")` }} />
                <div className="photo-text">{this.props.name}</div>
                <div className="photo-bg" />
            </div>
        )
    }
}

export default Photograph;
