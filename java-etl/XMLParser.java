import javax.xml.parsers.*;
import org.w3c.dom.*;
import java.io.File;
import java.util.*;

public class XMLParser {

    public static List<Map<String, String>> parse(String filePath, String tagName) {
        List<Map<String, String>> records = new ArrayList<>();

        try {
            File file = new File(filePath);
            DocumentBuilderFactory dbFactory = DocumentBuilderFactory.newInstance();
            DocumentBuilder dBuilder = dbFactory.newDocumentBuilder();
            Document doc = dBuilder.parse(file);

            doc.getDocumentElement().normalize();

            NodeList nodeList = doc.getElementsByTagName(tagName);

            for (int i = 0; i < nodeList.getLength(); i++) {

                Node node = nodeList.item(i);

                if (node.getNodeType() == Node.ELEMENT_NODE) {
                    Element element = (Element) node;

                    Map<String, String> row = new HashMap<>();

                    NodeList children = element.getChildNodes();

                    for (int j = 0; j < children.getLength(); j++) {
                        Node child = children.item(j);

                        if (child.getNodeType() == Node.ELEMENT_NODE) {
                            row.put(child.getNodeName(), child.getTextContent());
                        }
                    }

                    records.add(row);
                }
            }

        } catch (Exception e) {
            System.out.println("Error reading XML: " + e.getMessage());
        }

        return records;
    }
}