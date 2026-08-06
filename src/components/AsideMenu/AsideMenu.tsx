import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faHouse } from "@fortawesome/free-solid-svg-icons";
import MenuList from "../MenuList/MenuList";

function AsideMenu() {
    return (
        <>
            <aside id="asideNav" className="bg-primary text-white p-3">
                <h2><FontAwesomeIcon icon={faHouse} /> Menu</h2>

                <ul>
                    <MenuList link="" title="Inicio"/>
                    <MenuList link="" title="Ventas"/>
                    <MenuList link="" title="Citas"/>
                    <MenuList link="" title="inicio"/>
                </ul>
            </aside>
        </>
    );
}

export default AsideMenu;