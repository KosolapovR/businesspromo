import React, {useState} from 'react';
import {connect} from "react-redux";
import {Redirect, useParams} from "react-router-dom";
import AddForm from "../../components/addForm/AddForm";
import {createBook} from "../../state/books";
import {createAuthor} from "../../state/authors";
import {reset} from "redux-form";
import TransitionAlert from "../../components/transitionAlert";
import Paper from "@material-ui/core/Paper";
import Typography from "@material-ui/core/Typography";
import {makeStyles} from "@material-ui/core/styles";

const useStyles = makeStyles({
    title: {
        width: 'max-content',
        padding: '10px',
        marginTop: '10px'
    }
});

function AddBookPage({createBook, createdBook, resetForm}) {

    const classes = useStyles();
    const [showAlert, setShowAlert] = useState(false);

    const {id} = useParams();

    const handleSubmit = ({name, year, rank}) => {
        createBook({name, year, rank, author_id: id});
        setShowAlert(true);
        resetForm();
    };

    return (
        <div>
            {createdBook && <TransitionAlert type="книга" author={createdBook} show={showAlert}/>}
            <Paper className={classes.title}>
                <Typography>
                    Добавление новой книги
                </Typography>
            </Paper>
            {isNaN(id) ?
                <Redirect to={"/"}/> :
                < AddForm
                    onSubmit={handleSubmit}
                    nameLabel="Наименование"
                    yearLabel="Год"
                />
            }
        </div>

    );
}

const mapStateToProps = state => ({
    author: state.authors.author,
    createdBook: state.books.createdBook
});

function mapDispatchToProps(dispatch) {
    return {
        createBook: ({name, year, rank, author_id}) => dispatch(createBook({name, year, rank, author_id})),
        resetForm: () => dispatch(reset('addForm')),
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(AddBookPage);