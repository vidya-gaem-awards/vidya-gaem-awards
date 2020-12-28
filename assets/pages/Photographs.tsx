import React from "react";
import Photograph from "../components/Photograph";
import { Advertisement } from "../entities";

interface PhotographsProps {
    decorations: Advertisement[]
}

interface PhotographsState extends PhotographsProps {}

class Photographs extends React.Component<PhotographsProps, PhotographsState> {

    constructor(props) {
        super(props)

        this.state = {
            decorations: props.decorations
        }
    }

    render() {
        return (
            <div className="col">
                <h1 className="page-header board-header">Voting Page Photographs</h1>

                <div className="text-center">
                    <button className="btn btn-sm btn-primary" id="new-award" type="button" data-toggle="modal"
                            data-target="#dialog-edit">
                        <i className="fal fa-fw fa-plus" /> Add a new photo
                    </button>
                </div>

                <div className="decoration-container">
                    {this.state.decorations.map((decoration) => (
                        <Photograph id={decoration.id} name={decoration.name} image={decoration.image.url} key={decoration.id} />
                    ))}
                </div>

            </div>
        )
    }
}

export default Photographs;
