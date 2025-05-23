import React, { useRef } from 'react';
import {
    Box,
    Typography,
    useTheme,
    InputBase,
    Avatar,
    Button,
    Dialog,
    DialogActions,
    DialogContent,
    CircularProgress,
    useMediaQuery,
    Grid
} from '@mui/material';
import { makeStyles } from '@mui/styles';
import { Link } from 'react-router-dom';
import { Formik, Form } from 'formik';
import * as Yup from 'yup';
import { useSelector } from 'react-redux';

//icons
import SearchIcon from '@mui/icons-material/Search';
import { BiEditAlt } from 'react-icons/bi';
import CloseIcon from '@mui/icons-material/Close';
import Success from '../../../ui-component/modals/Success';
import ResumeUpload from '../../../components/reusables/forms/ResumeUpload';
import Textfield from '../../../components/reusables/FormUI/Textfield';
import api from '../../../helpers/api';
import EmptyPages from '../../../components/common/EmptyPages';
import Warning from '../../../ui-component/modals/Warning';

const useStyles = makeStyles((theme) => ({
    profile_cover_img: {
        height: '160px',
        minWidth: '100%',
        borderRadius: '6px',
        width: '100%',
        overflowY: 'hidden !important',
        overflowX: 'hidden !important',
        '& img': {
            objectFit: 'cover !important',
            height: '160px',
            width: '100%',
            objectPosition: '10% 40%',
            borderBottomRightRadius: '6px',
            borderBottomLeftRadius: '6px'
        }
    }
}));

export const IconsText = ({ icon, text }) => {
    const theme = useTheme();

    return (
        <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', gap: '2px' }}>
            <>{icon} </> <Typography sx={{ ...theme.typography.caption, fontWeight: '600' }}>{text}</Typography>
        </Box>
    );
};

export const Picturebox2 = ({ image_title, image, date, setEdit, value = {}, clicked, from }) => {
    const { profile_cover_img } = useStyles();
    const theme = useTheme();

    return (
        <div
            style={{
                background: '#F5F5F5',
                position: 'relative',
                height: '230px',
                width: '200px',
                borderBottomRightRadius: '6px',
                borderBottomLeftRadius: '6px'
            }}
        >
            <Box className={profile_cover_img}>
                <img alt="bio" src={`${api.base_image_path}/${from}/${image}`} />
            </Box>
            <Box
                sx={{
                    ...theme.typography.column,
                    justifyContent: 'center',
                    height: '70px',
                    paddingLeft: '10px',
                    '& > :nth-child(1)': {
                        fontSize: '1rem',
                        fontWeight: '600'
                    },
                    '& > :nth-child(2)': {
                        fontSize: '0.9rem'
                    }
                }}
            >
                <Typography> {image_title} </Typography>
                <Typography> {date} </Typography>
            </Box>
            <Box sx={{ position: 'absolute', top: 1, right: 5 }}>
                <BiEditAlt
                    style={{ color: theme.palette.secondary.main1 }}
                    onClick={async () => {
                        await setEdit(() => {
                            return { ...value, show: true };
                        });
                        clicked(true);
                    }}
                />
            </Box>
        </div>
    );
};

function promiseState(p) {
    const t = {};
    return Promise.race([p, t]).then(
        (v) => (v === t ? 'pending' : 'fulfilled'),
        () => 'rejected'
    );
}

const rebuildData = (data, file) => {
    let formData = new FormData();
    formData.append('image_title', data.gallery_title);
    formData.append('year', data.gallery_year);
    formData.append('gallery', file);
    return formData;
};

export const LowerButton = () => {
    const theme = useTheme();
    return (
        <Box sx={{ ...theme.typography.flex, background: 'white', paddingBottom: '30px' }}>
            <Button sx={{ padding: '7px 70px' }} disableElevation variant="contained" to="/enterprise" LinkComponent={Link}>
                Create Page
            </Button>
        </Box>
    );
};
export default function SchoolGalleryTab({ data, setReload }) {
    const theme = useTheme();
    const { schools } = useSelector((state) => state.userDataReducer.enterprise_ids);
    const filesharhe_ref = useRef();
    const matches = useMediaQuery('(min-width:900px)');
    const [picturesOpen, setPicturesOpen] = React.useState(false);
    const [pictureStep, setPictureStep] = React.useState(0);
    const [edit, setEdit] = React.useState({ show: false });
    const [modalMessage, setModalMessage] = React.useState('');
    const [spinner, setSpinner] = React.useState(false);
    const [fetchError, setFetchError] = React.useState(false);
    const [refresh, setRefresh] = React.useState(false);

    if (schools.length === 0) {
        return (
            <Box>
                <EmptyPages label="School Page" />
                <LowerButton />
            </Box>
        );
    }

    const handlePictures = () => {
        setPicturesOpen(false);
        window.scrollTo({
            top: 0,
            left: 0,
            behavior: 'smooth'
        });
        setEdit((prev) => {
            return { ...prev, show: false };
        });
        setPictureStep(0);
        if (refresh) {
            setReload((prev) => prev + 1);
        }

        setRefresh(false);
    };

    const delGallery = async () => {
        setSpinner(true);
        api.deleteSchoolGallery({ id: edit.gallery_id })
            .catch(async (error) => {})
            .then(async () => {
                await setRefresh(true);
                setReload((prev) => prev + 1);
            });
        setSpinner(false);
    };

    return (
        <Box
            sx={
                {
                    //  borderBottom: '1px solid red'
                }
            }
        >
            <Box
                sx={{
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center',
                    '@media (max-width: 453px)': {
                        flexDirection: 'column',
                        alignItems: 'flex-start',
                        gap: '15px'
                    }
                }}
            >
                {/* box 1 */}
                <Box>
                    <Typography sx={{ ...theme.typography.title3, fontWeight: '600' }}>Galleries</Typography>
                    <Box sx={{ display: 'flex', mt: '15px' }}>
                        <Button
                            disableElevation
                            sx={{ textTransform: 'capitalize', borderRadius: '0px', height: '39px' }}
                            variant="contained"
                        >
                            All
                        </Button>

                        <Box
                            sx={{
                                padding: '0 5px',
                                background: 'white',
                                height: '39px',
                                display: 'flex',
                                alignItems: 'center',
                                marginLeft: '15px',
                                border: '1px solid #C4C4C4',
                                '&:hover': {
                                    border: '1px solid  rgba(0, 0, 0, 0.87)'
                                }
                            }}
                        >
                            <InputBase
                                placeholder="Try “Lagos Branch”"
                                sx={{
                                    height: '30px',
                                    width: '100%',
                                    background: 'white'
                                }}
                            />
                            <Avatar
                                sx={{ cursor: 'pointer', bgcolor: theme.palette.primary.main, height: '30px', width: '30px' }}
                                variant="square"
                            >
                                <SearchIcon fontSize="small" />
                            </Avatar>
                        </Box>
                    </Box>
                </Box>
                {/* box 2 */}
                <Button
                    onClick={setPicturesOpen}
                    sx={{ textTransform: 'capitalize', padding: '7px 20px !important', height: 'max-content' }}
                    disableElevation
                    variant="contained"
                >
                    Add Gallery
                </Button>
            </Box>
            {data.length !== 0 ? (
                <Box
                    sx={{
                        display: 'flex',
                        flexWrap: 'wrap',
                        gap: '20px',
                        mt: '30px',
                        bgcolor: 'white',
                        padding: '20px',
                        borderRadius: '10px',
                        '@media (max-width: 453px)': {
                            justifyContent: 'center'
                        }
                    }}
                >
                    {data.map((item) => (
                        <Picturebox2 value={item} setEdit={setEdit} clicked={setPicturesOpen} from="schoolgalleries" {...item} />
                    ))}
                </Box>
            ) : (
                <Box sx={{ ...theme.typography.flex, fontWeight: '600', height: '200px' }}> No Galleries yet </Box>
            )}

            <Dialog
                open={picturesOpen}
                onClose={handlePictures}
                aria-labelledby="scroll-dialog-title"
                aria-describedby="scroll-dialog-description"
            >
                <DialogContent>
                    <Grid container>
                        <Grid item xs={12}>
                            <Box sx={{ display: 'flex', justifyContent: 'space-between' }}>
                                <Typography sx={{ fontSize: '1.2rem' }}>Add Pictures</Typography>
                                <CloseIcon sx={{ cursor: 'pointer' }} onClick={handlePictures} />
                            </Box>
                        </Grid>
                    </Grid>
                    {/* ii */}
                    {edit.show ? (
                        <PortfolioStepper portfolioStep={pictureStep} setPortfolioStep={setPictureStep}>
                            <Grid container>
                                <Grid item xs={12}>
                                    <Formik
                                        initialValues={{
                                            gallery_title: edit.image_title,
                                            gallery_year: edit.date
                                        }}
                                        onSubmit={async (values) => {
                                            setSpinner(true);
                                            api.updateSchoolGallery(edit.gallery_id, {
                                                image_title: values.gallery_title,
                                                year: values.gallery_year
                                            })
                                                .catch(async (error) => {
                                                    await setFetchError(true);
                                                    await console.log('something went wrong');
                                                })
                                                .then(async () => {
                                                    await setFetchError(false);
                                                    await setRefresh(true);
                                                    setReload((prev) => prev + 1);
                                                });

                                            if (!fetchError) {
                                                await setModalMessage(
                                                    'You have successfully update this portfolio item You can go to your dashboard now'
                                                );
                                                setPictureStep((step) => step + 2);
                                            }
                                            setFetchError(false);
                                            setSpinner(false);
                                        }}
                                        validationSchema={Yup.object().shape({
                                            gallery_title: Yup.string().required('Title is Required'),
                                            gallery_year: Yup.string().required('Gallery year is Required')
                                        })}
                                    >
                                        {({ isSubmitting }) => (
                                            <Form autoComplete="off">
                                                <Grid container>
                                                    <Grid
                                                        sx={{
                                                            paddingRight: '20px',
                                                            '@media (max-width: 900px)': {
                                                                padding: '0px'
                                                            }
                                                        }}
                                                        item
                                                        xs={12}
                                                        md={6}
                                                    >
                                                        <Textfield name="gallery_title" helpertext="Title" />
                                                    </Grid>
                                                    <Grid sx={{ paddingLeft: matches ? '20px' : '0px' }} item xs={12} md={6}>
                                                        <Textfield name="gallery_year" helpertext="Year" />
                                                    </Grid>

                                                    <Grid xs={12} item>
                                                        <Box sx={{ ...theme.typography.flex }}>
                                                            <DialogActions
                                                                sx={{
                                                                    display: 'flex',
                                                                    gap: '20px',
                                                                    '@media (max-width: 513px)': {
                                                                        gap: '10px'
                                                                    }
                                                                }}
                                                            >
                                                                <Button
                                                                    startIcon={
                                                                        spinner ? <CircularProgress color="secondary" size="1rem" /> : null
                                                                    }
                                                                    sx={{
                                                                        width: '200px',
                                                                        marginTop: '20px',
                                                                        letterSpacing: '1px',
                                                                        borderRadius: '0px',
                                                                        color: 'rgb(217, 38, 39)',
                                                                        border: '1px solid rgb(217, 38, 39)',
                                                                        textTransform: 'capitalize',
                                                                        '&:hover': {
                                                                            border: '1px solid rgb(217, 38, 39)'
                                                                        },
                                                                        [theme.breakpoints.down('sm')]: {
                                                                            marginTop: '30px'
                                                                        },
                                                                        '@media (max-width: 513px)': {
                                                                            width: '120px'
                                                                        }
                                                                    }}
                                                                    disableElevation
                                                                    variant="outlined"
                                                                    onClick={() => setPictureStep((step) => step + 1)}
                                                                >
                                                                    Delete
                                                                </Button>
                                                                <Button
                                                                    startIcon={
                                                                        isSubmitting ? (
                                                                            <CircularProgress color="secondary" size="1rem" />
                                                                        ) : null
                                                                    }
                                                                    sx={{
                                                                        width: '200px',
                                                                        marginTop: '20px',
                                                                        letterSpacing: '1px',
                                                                        borderRadius: '0px',
                                                                        color: 'white',
                                                                        textTransform: 'capitalize',
                                                                        '& :hover': {
                                                                            color: 'black'
                                                                        },
                                                                        [theme.breakpoints.down('sm')]: {
                                                                            marginTop: '30px'
                                                                        },
                                                                        '@media (max-width: 513px)': {
                                                                            width: '120px'
                                                                        }
                                                                    }}
                                                                    disableElevation
                                                                    variant="contained"
                                                                    type="submit"
                                                                >
                                                                    Update
                                                                </Button>
                                                            </DialogActions>
                                                        </Box>
                                                    </Grid>
                                                </Grid>
                                            </Form>
                                        )}
                                    </Formik>
                                </Grid>
                            </Grid>
                            <Warning
                                load={spinner}
                                onNoClick={() => setPictureStep((step) => step - 1)}
                                onYesClick={async () => {
                                    delGallery();
                                    await setModalMessage('You have successfully delete a Gallery item You can go to your dashboard now');

                                    setPictureStep((step) => step + 1);
                                }}
                                text="Are you sure you want to delete this Portfolio information from your portfolio list."
                            />
                            <Success onclick={handlePictures} text="Refresh Gallery" content={modalMessage} />
                        </PortfolioStepper>
                    ) : (
                        <PortfolioStepper portfolioStep={pictureStep} setPortfolioStep={setPictureStep}>
                            <Grid container>
                                <Grid item xs={12}>
                                    <Formik
                                        initialValues={{
                                            gallery_title: '',
                                            gallery_year: '',
                                            gallery_image: ''
                                        }}
                                        onSubmit={async (values) => {
                                            setSpinner(true);
                                            var formData = rebuildData(values, filesharhe_ref.current.files[0]);
                                            //  await dispatch(addPortfolio(formData));
                                            api.createSchoolGallery(schools[0]?.school_id, formData).then(async () => {
                                                await setRefresh(true);

                                                setPictureStep((step) => step + 1);
                                            });

                                            setSpinner(false);
                                        }}
                                        validationSchema={Yup.object().shape({
                                            gallery_title: Yup.string().required('Title is Required'),
                                            gallery_year: Yup.string().required('Gallery year is Required'),
                                            gallery_image: Yup.string().required('Gallery image Institution is Required')
                                        })}
                                    >
                                        {({ isSubmitting }) => (
                                            <Form autoComplete="off">
                                                <Grid container>
                                                    <Grid
                                                        sx={{
                                                            paddingRight: '20px',
                                                            '@media (max-width: 900px)': {
                                                                padding: '0px'
                                                            }
                                                        }}
                                                        item
                                                        xs={12}
                                                        md={6}
                                                    >
                                                        <Textfield name="gallery_title" helpertext="Title" />
                                                    </Grid>
                                                    <Grid sx={{ paddingLeft: matches ? '20px' : '0px' }} item xs={12} md={6}>
                                                        <Textfield name="gallery_year" helpertext="Year" />
                                                    </Grid>
                                                    <Grid mt="5px" xs={12}>
                                                        <Typography variant="caption">Upload Image</Typography>
                                                        <Box sx={{ mt: '5px' }}>
                                                            <ResumeUpload name="gallery_image" ref={filesharhe_ref} />
                                                        </Box>
                                                    </Grid>

                                                    <Grid xs={12} item>
                                                        <Box sx={{ ...theme.typography.flex }}>
                                                            <DialogActions>
                                                                <Button
                                                                    startIcon={
                                                                        isSubmitting ? (
                                                                            <CircularProgress color="secondary" size="1rem" />
                                                                        ) : null
                                                                    }
                                                                    sx={{
                                                                        width: '200px',
                                                                        marginTop: '20px',
                                                                        letterSpacing: '1px',
                                                                        borderRadius: '0px',
                                                                        color: 'white',
                                                                        textTransform: 'capitalize',
                                                                        '& :hover': {
                                                                            color: 'black'
                                                                        },
                                                                        [theme.breakpoints.down('sm')]: {
                                                                            marginTop: '30px'
                                                                        }
                                                                    }}
                                                                    disableElevation
                                                                    variant="contained"
                                                                    type="submit"
                                                                >
                                                                    Save
                                                                </Button>
                                                            </DialogActions>
                                                        </Box>
                                                    </Grid>
                                                </Grid>
                                            </Form>
                                        )}
                                    </Formik>
                                </Grid>
                            </Grid>
                            <Success
                                onclick={handlePictures}
                                text="Refresh Gallery"
                                content="You have successfully added a gallery item
You can go to your dashboard now."
                            />
                        </PortfolioStepper>
                    )}
                </DialogContent>
            </Dialog>
        </Box>
    );
}

export function PortfolioStepper({ children, portfolioStep, setPortfolioStep, ...props }) {
    const childrenArray = React.Children.toArray(children);
    const currentChild = childrenArray[portfolioStep];

    return <Box>{currentChild}</Box>;
}
