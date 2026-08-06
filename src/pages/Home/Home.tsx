import AsideMenu from "../../components/AsideMenu/AsideMenu";
import Header from "../../components/Header/Header";
import MainContent from "../../components/MainContent/MainContent";

function Home() {
    return (
        <>
            <AsideMenu />
            <div id="contenedor">
                <Header title="Inicio" />
                <MainContent />
            </div>
        </>
    );
}

export default Home;