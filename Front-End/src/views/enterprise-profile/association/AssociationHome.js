import React, { useRef } from 'react';
import { Box, Grid, Avatar, Typography, useTheme, Button, Dialog, DialogActions, DialogContent, CircularProgress } from '@mui/material';
import { useDispatch, useSelector } from 'react-redux';
import useMediaQuery from '@mui/material/useMediaQuery';
import { makeStyles } from '@mui/styles';
import { Formik, Form } from 'formik';
import * as Yup from 'yup';
import { BiEditAlt } from 'react-icons/bi';
import FullWidthTabs from './tabs';
import { Link } from 'react-router-dom';
import CloseIcon from '@mui/icons-material/Close';
import SubCard from '../../../ui-component/cards/SubCard';
import { updateProfilePicture, updateCoverPicture } from '../../../store/actions/userDataActions';
import ResumeUpload from '../../../components/reusables/forms/ResumeUpload';
import api from '../../../helpers/api';

const useStyles = makeStyles((theme) => ({
    root: {
        width: '100%'
    },
    profile_cover_img: {
        height: '25vh',
        position: 'relative',
        zIndex: '1',
        minWidth: '100%',
        borderRadius: '10px',
        width: '100%',
        overflowY: 'hidden !important',
        overflowX: 'hidden !important',
        '& img': {
            objectFit: 'cover !important',
            width: '100%',
            objectPosition: '10% 40%'
        }
    }
}));

const FILE_SIZE = 1000000;
const SUPPORTED_FORMATS = ['image/jpg', 'image/jpeg', 'image/gif', 'image/png'];

const AssociationHome = () => {
    const {
        user: { bio }
    } = useSelector((state) => state.authReducer);
    const { associations } = useSelector((state) => state.userDataReducer.enterprise_ids);

    const dispatch = useDispatch();
    const { root } = useStyles();
    const theme = useTheme();
    const filesharhe_ref = useRef();
    const matchDownMd = useMediaQuery('(min-width:600px)');
    const [open, setOpen] = React.useState(false);
    const [step, setStep] = React.useState(0);
    const [association, setAssociation] = React.useState({});
    const [message, setMessage] = React.useState(false);
    const [cover, setCover] = React.useState(false);

    const handleClose = () => {
        setOpen(false);
    };

    const handleMessageClose = () => {
        setMessage(false);
    };
    const handleCoverClose = () => {
        setCover(false);
    };
    const handleClickOpen = () => {
        setMessage(true);
        setStep(0);
        //setScroll(scrollType);
    };
    const handleCoverOpen = () => {
        setCover(true);
        setStep(0);
        //setScroll(scrollType);
    };

    const descriptionElementRef = React.useRef(null);
    React.useEffect(() => {
        if (open) {
            const { current: descriptionElement } = descriptionElementRef;
            if (descriptionElement !== null) {
                descriptionElement.focus();
            }
        }
    }, [open]);

    React.useEffect(() => {
        setAssociation(associations[0]);
    }, []);

    return (
        <Box className={root}>
            <Grid container>
                <Grid
                    sx={{ position: 'relative', overflowY: 'hidden !important', overflowX: 'hidden !important', borderRadius: '10px' }}
                    item
                >
                    <Avatar
                        alt="Remy Sharp"
                        src={`${api.base_image_path}${association.cover_image}`}
                        sx={{
                            height: '25vh',
                            position: 'relative',
                            zIndex: '1',
                            minWidth: '100%',
                            borderRadius: '10px',
                            width: '100vw',
                            overflowY: 'hidden !important',
                            overflowX: 'hidden !important',
                            '& img': {
                                objectFit: 'cover !important',
                                width: '100%',
                                objectPosition: '10% 40%',
                                overflowY: 'hidden !important',
                                overflowX: 'hidden !important'
                            }
                        }}
                    />
                    <Typography
                        onClick={handleCoverOpen}
                        sx={{
                            cursor: 'pointer',
                            position: 'absolute',
                            color: 'white',
                            bottom: 10,
                            right: 50,
                            zIndex: 2,
                            textDecoration: 'underline',
                            [theme.breakpoints.down('sm')]: {
                                right: 10
                            }
                        }}
                    >
                        Add Cover Image
                    </Typography>
                </Grid>
                {/* profile */}
                <Grid item xs={12} sm={5} md={4} lg={3}>
                    <Grid container>
                        <Grid xs={7} sm={12} item>
                            <Box
                                sx={{
                                    ...theme.typography.flex,
                                    height: '240px',
                                    background: '#0991FF',
                                    border: 'none',
                                    marginTop: '-8px',
                                    color: 'white',
                                    position: 'relative',
                                    overFlow: 'visible !important',
                                    borderBottomLeftRadius: '10px',
                                    borderBottomRightRadius: '10px'
                                }}
                            >
                                <Box>
                                    <Avatar
                                        onClick={handleClickOpen}
                                        alt="Remy Sharp"
                                        src={`${api.base_image_path}${association.logo}`}
                                        sx={{
                                            cursor: 'pointer',
                                            width: 150,
                                            height: 150,
                                            position: 'absolute',
                                            zIndex: 4,
                                            top: -60,
                                            left: 60,
                                            [theme.breakpoints.down('sm')]: {
                                                left: '16%'
                                            }
                                        }}
                                    />
                                </Box>
                                <Box mt="78px">
                                    <Typography sx={{ ...theme.typography.heading, fontWeight: 'bold', textAlign: 'center' }}>
                                        {association.association_name}
                                    </Typography>
                                    {/* <Typography sx={{ textAlign: 'center', fontSize: '0.8rem' }}>
                                        {bio.profession} {bio.employment_status === 'self_employed' && 'at Self Employed'}{' '}
                                        {bio.employment_status === 'employed' && `at ${bio.current_employer}`}
                                    </Typography> */}
                                </Box>
                            </Box>
                        </Grid>
                        {!matchDownMd && (
                            <Grid xs={5} item>
                                <Box sx={{ display: 'flex', justifyContent: 'center', textTransform: 'capitalize', mt: '20px' }}>
                                    <Button
                                        LinkComponent={Link}
                                        to="/update-association"
                                        startIcon={<BiEditAlt />}
                                        disableElevation
                                        variant="contained"
                                        state={{ from: 'profile' }}
                                        sx={{ textTransform: 'capitalize', background: '#0991FF' }}
                                    >
                                        Edit
                                    </Button>
                                </Box>
                            </Grid>
                        )}
                    </Grid>

                    <SubCard divider={false} p={true} sx={{ marginTop: '30px' }} title="Bio">
                        <Box sx={{ display: 'flex', padding: '5px 10px', background: '#CEE9FF' }}>
                            <Typography sx={{ fontSize: '0.8rem' }}> Association Type: </Typography>
                            <Typography sx={{ fontSize: '0.8rem', fontWeight: 'bold', color: '#0991FF', ml: '5px' }}>
                                {association.association_type}
                            </Typography>
                        </Box>
                        <Box sx={{ display: 'flex', mt: '5px', padding: '5px 10px' }}>
                            <Typography sx={{ fontSize: '0.8rem' }}> Location: </Typography>
                            <Typography sx={{ fontSize: '0.8rem', fontWeight: 'bold', color: '#0991FF', ml: '5px' }}>
                                {association.main_office_location_lga} {association.main_office_location_state}
                            </Typography>
                        </Box>
                        <Box sx={{ display: 'flex', mt: '5px', background: '#CEE9FF', padding: '5px 10px' }}>
                            <Typography sx={{ fontSize: '0.8rem' }}> License No: </Typography>
                            <Typography sx={{ fontSize: '0.8rem', fontWeight: 'bold', color: '#0991FF', ml: '5px' }}>
                                {association.cac}
                            </Typography>
                        </Box>
                        <Box sx={{ display: 'flex', mt: '5px', padding: '5px 10px' }}>
                            <Typography sx={{ fontSize: '0.8rem' }}> Website: </Typography>
                            <Typography sx={{ fontSize: '0.8rem', fontWeight: 'bold', color: '#0991FF', ml: '5px' }}>
                                {association.website}
                            </Typography>
                        </Box>
                        <Box sx={{ display: 'flex', mt: '5px', background: '#CEE9FF', padding: '5px 10px' }}>
                            <Typography sx={{ fontSize: '0.8rem' }}> Email Address: </Typography>
                            <Typography sx={{ fontSize: '0.8rem', fontWeight: 'bold', color: '#0991FF', ml: '5px' }}>
                                {association.association_contact_email}
                            </Typography>
                        </Box>
                        <Box sx={{ display: 'flex', mt: '5px', padding: '5px 10px 0px 10px' }}>
                            <Typography sx={{ fontSize: '0.8rem' }}> association Size: </Typography>
                            <Typography sx={{ fontSize: '0.8rem', fontWeight: 'bold', color: '#0991FF', ml: '5px' }}>
                                {association.association_size}
                            </Typography>
                        </Box>
                        <Box sx={{ display: 'flex', mt: '5px', background: '#CEE9FF', padding: '5px 10px' }}>
                            <Typography sx={{ fontSize: '0.8rem' }}> Founded: </Typography>
                            <Typography sx={{ fontSize: '0.8rem', fontWeight: 'bold', color: '#0991FF', ml: '5px' }}>
                                {new Date(association.founded_year).getFullYear()}
                            </Typography>
                        </Box>
                    </SubCard>

                    {/* edit profile button */}
                    {matchDownMd && (
                        <Grid xs={12} item>
                            <Box sx={{ display: 'flex', justifyContent: 'center', textTransform: 'capitalize', mt: '20px' }}>
                                <Button
                                    LinkComponent={Link}
                                    to="/update-association"
                                    startIcon={<BiEditAlt />}
                                    disableElevation
                                    variant="contained"
                                    state={{ from: 'profile' }}
                                    sx={{ textTransform: 'capitalize', background: '#0991FF', width: '100%', padding: '15px 0' }}
                                >
                                    Edit Page
                                </Button>
                            </Box>
                        </Grid>
                    )}
                </Grid>

                {/* left section */}
                <Grid item xs={12} sm={7} md={8} lg={9}>
                    <SubCard
                        divider={true}
                        sx={{
                            ml: '20px',
                            mt: '20px',
                            minHeight: '213px',
                            height: 'auto',
                            [theme.breakpoints.down('sm')]: {
                                ml: '0px'
                            }
                        }}
                        title="About"
                    >
                        <Typography sx={{ fontSize: '0.9rem', color: '#333333', ml: '5px' }}>{association.about}</Typography>
                    </SubCard>

                    {/* tabs */}
                    <Box sx={{ width: '100%' }}>
                        <FullWidthTabs />
                    </Box>
                </Grid>
            </Grid>

            {/* profile image dialog */}
            <Dialog
                fullScreen
                open={message}
                onClose={handleMessageClose}
                aria-labelledby="scroll-dialog-title"
                aria-describedby="scroll-dialog-description"
            >
                {/* <DialogTitle id="scroll-dialog-title">Message</DialogTitle> */}
                {/* <Box sx={{ ...theme.typography.flex, flexDirection: 'column' }}> */}
                <DialogContent sx={{ paddingTop: '20vh' }}>
                    <Stepper step={step} setStep={setStep}>
                        <>
                            <Grid container>
                                <Grid item xs={12}>
                                    <Box sx={{ ...theme.typography.column, alignItems: 'center' }}>
                                        <Typography sx={{ fontWeight: '600', mb: '10px' }}>Profile Picture</Typography>
                                        <Avatar
                                            alt="Remy Sharp"
                                            src={`${api.base_image_path}${association.logo}`}
                                            sx={{
                                                cursor: 'pointer',
                                                width: 250,
                                                height: 250,
                                                borderRadius: '3px'
                                            }}
                                        />
                                    </Box>
                                </Grid>
                                <Grid item xs={12}>
                                    <Box
                                        sx={{
                                            ...theme.typography.flex,
                                            width: '100%',
                                            justifyContent: 'center',
                                            gap: '10px',
                                            mt: '20px'
                                        }}
                                    >
                                        <Button
                                            disableElevation
                                            sx={{ padding: '8px 60px', textTransform: 'capitalize' }}
                                            variant="contained"
                                            // onClick={handleMessageClose}
                                            onClick={() => setStep(step + 1)}
                                        >
                                            Change Profile Picture
                                        </Button>
                                    </Box>
                                </Grid>
                            </Grid>
                        </>

                        {/* second box */}
                        <Box>
                            <Grid container>
                                <Grid item xs={12}>
                                    <Formik
                                        initialValues={{
                                            profile_picture: ''
                                        }}
                                        onSubmit={async (values) => {
                                            console.log(values);

                                            let formData = new FormData();
                                            formData.append('logo', filesharhe_ref.current.files[0]);

                                            await api.changeAssociationLogo(association.association_id, formData).then(async () => {
                                                window.location.reload();
                                            });
                                        }}
                                        validationSchema={Yup.object().shape({
                                            profile_picture: Yup.mixed()
                                                .required('A file is required')
                                                .test('fileSize', 'File too large', (value) =>
                                                    value && filesharhe_ref.current
                                                        ? filesharhe_ref.current.files[0].size <= FILE_SIZE
                                                            ? true
                                                            : false
                                                        : true
                                                )
                                                .test('fileFormat', 'Unsupported Format', (value) => {
                                                    //  console.log(filesharhe_ref.current.files[0].size);
                                                    return value && filesharhe_ref.current
                                                        ? SUPPORTED_FORMATS.includes(filesharhe_ref.current.files[0].type)
                                                            ? true
                                                            : false
                                                        : true;
                                                })
                                        })}
                                    >
                                        {(formik) => (
                                            <Form autoComplete="off">
                                                <Grid sx={{ ...theme.typography.flex }} container>
                                                    <Grid item xs={10}>
                                                        <Typography sx={{ color: theme.palette.textColor, mb: '10px' }}>
                                                            Upload a new Profile Picture
                                                        </Typography>
                                                        <ResumeUpload name="profile_picture" ref={filesharhe_ref} />
                                                    </Grid>

                                                    <Grid xs={12} item>
                                                        <Box sx={{ ...theme.typography.flex }}>
                                                            <DialogActions>
                                                                <Button
                                                                    startIcon={
                                                                        formik.isSubmitting ? (
                                                                            <CircularProgress color="secondary" size="1rem" />
                                                                        ) : null
                                                                    }
                                                                    sx={{
                                                                        //  width: '200px',
                                                                        marginTop: '20px',
                                                                        letterSpacing: '1px',
                                                                        // borderRadius: '0px',
                                                                        padding: '8px 60px',
                                                                        color: 'white',
                                                                        textTransform: 'capitalize',
                                                                        '& :hover': {
                                                                            color: 'black'
                                                                        },
                                                                        [theme.breakpoints.down('sm')]: {
                                                                            marginTop: '20px'
                                                                        }
                                                                    }}
                                                                    disableElevation
                                                                    variant="contained"
                                                                    type="submit"
                                                                >
                                                                    Save Profile Picture
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
                        </Box>
                    </Stepper>
                </DialogContent>
                <div onClick={handleMessageClose} style={{ position: 'absolute', top: 20, right: 30 }}>
                    <CloseIcon
                        sx={{
                            color: 'red',
                            height: '40px',
                            width: '40px',
                            cursor: 'pointer',
                            [theme.breakpoints.down('sm')]: {
                                height: '20px',
                                width: '20px'
                            }
                        }}
                    />
                </div>
            </Dialog>

            {/* cover image dialog */}

            <Dialog
                fullScreen
                open={cover}
                onClose={handleCoverClose}
                aria-labelledby="scroll-dialog-title"
                aria-describedby="scroll-dialog-description"
            >
                <DialogContent sx={{ paddingTop: '20vh' }}>
                    <Stepper step={step} setStep={setStep}>
                        <>
                            <Grid container>
                                <Grid item xs={12}>
                                    <Box sx={{ ...theme.typography.column, alignItems: 'center' }}>
                                        <Typography sx={{ fontWeight: '600', mb: '10px' }}>Cover Image</Typography>
                                        <Avatar
                                            alt="Remy Sharp"
                                            src={`${api.base_image_path}${association.cover_image}`}
                                            sx={{
                                                cursor: 'pointer',
                                                width: 250,
                                                height: 250,
                                                borderRadius: '3px'
                                            }}
                                        />
                                    </Box>
                                </Grid>
                                <Grid item xs={12}>
                                    <Box
                                        sx={{
                                            ...theme.typography.flex,
                                            width: '100%',
                                            justifyContent: 'center',
                                            gap: '10px',
                                            mt: '20px'
                                        }}
                                    >
                                        <Button
                                            disableElevation
                                            sx={{ padding: '8px 60px', textTransform: 'capitalize' }}
                                            variant="contained"
                                            // onClick={handleMessageClose}
                                            onClick={() => setStep(step + 1)}
                                        >
                                            Change Cover Image
                                        </Button>
                                    </Box>
                                </Grid>
                            </Grid>
                        </>

                        {/* second box */}
                        <Box>
                            <Grid container>
                                <Grid item xs={12}>
                                    <Formik
                                        initialValues={{
                                            coverimage: ''
                                        }}
                                        onSubmit={async (values) => {
                                            console.log(values);

                                            let formData = new FormData();
                                            formData.append('coverimage', filesharhe_ref.current.files[0]);

                                            await api.changeAssociationCover(association.association_id, formData).then(async () => {
                                                window.location.reload();
                                            });
                                        }}
                                        validationSchema={Yup.object().shape({
                                            coverimage: Yup.mixed()
                                                .required('A file is required')
                                                .test('fileSize', 'File too large', (value) =>
                                                    value && filesharhe_ref.current
                                                        ? filesharhe_ref.current.files[0].size <= FILE_SIZE
                                                            ? true
                                                            : false
                                                        : true
                                                )
                                                .test('fileFormat', 'Unsupported Format', (value) => {
                                                    return value && filesharhe_ref.current
                                                        ? SUPPORTED_FORMATS.includes(filesharhe_ref.current.files[0].type)
                                                            ? true
                                                            : false
                                                        : true;
                                                })
                                        })}
                                    >
                                        {(formik) => (
                                            <Form autoComplete="off">
                                                <Grid sx={{ ...theme.typography.flex }} container>
                                                    <Grid item xs={10}>
                                                        <Typography sx={{ color: theme.palette.textColor, mb: '10px' }}>
                                                            Upload a new Cover Image
                                                        </Typography>
                                                        <ResumeUpload name="coverimage" ref={filesharhe_ref} />
                                                    </Grid>

                                                    <Grid xs={12} item>
                                                        <Box sx={{ ...theme.typography.flex }}>
                                                            <DialogActions>
                                                                <Button
                                                                    startIcon={
                                                                        formik.isSubmitting ? (
                                                                            <CircularProgress color="secondary" size="1rem" />
                                                                        ) : null
                                                                    }
                                                                    sx={{
                                                                        //  width: '200px',
                                                                        marginTop: '20px',
                                                                        letterSpacing: '1px',
                                                                        // borderRadius: '0px',
                                                                        padding: '8px 60px',
                                                                        color: 'white',
                                                                        textTransform: 'capitalize',
                                                                        '& :hover': {
                                                                            color: 'black'
                                                                        },
                                                                        [theme.breakpoints.down('sm')]: {
                                                                            marginTop: '20px'
                                                                        }
                                                                    }}
                                                                    disableElevation
                                                                    variant="contained"
                                                                    type="submit"
                                                                >
                                                                    Save Cover Image
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
                        </Box>
                    </Stepper>
                </DialogContent>
                <div onClick={handleCoverClose} style={{ position: 'absolute', top: 20, right: 30 }}>
                    <CloseIcon
                        sx={{
                            //   color: 'red',
                            height: '40px',
                            width: '40px',
                            cursor: 'pointer',
                            [theme.breakpoints.down('sm')]: {
                                height: '20px',
                                width: '20px'
                            }
                        }}
                    />
                </div>
            </Dialog>
        </Box>
    );
};

export default AssociationHome;

export function Stepper({ children, step, setStep, ...props }) {
    const childrenArray = React.Children.toArray(children);
    const currentChild = childrenArray[step];

    return <Box>{currentChild}</Box>;
}
